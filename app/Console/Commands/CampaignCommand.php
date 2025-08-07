<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Configuration;
use App\Services\PepipostV5Service;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use App\Models\EmailHistory;
use App\Services\SystemLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignCommand extends Command
{
    protected $signature = 'campaign {--batch-size=50 : Number of emails to process in each batch} {--max-concurrent=5 : Maximum concurrent API calls}';
    protected $description = 'Send Email Task with Optimized Performance';
    protected $logger;
    protected $batchSize;
    protected $maxConcurrent;
    protected $emailHistoryBatch = [];
    protected $campaignStats = []; // Tambahkan tracking per campaign

    public function __construct(SystemLogger $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function handle()
    {
        $startTime = Carbon::now();
        $emailsSent = 0;
        $campaignProcessed = 0;
        
        // Konfigurasi optimasi
        $this->batchSize = (int) $this->option('batch-size');
        $this->maxConcurrent = (int) $this->option('max-concurrent');

        $this->info("Starting optimized campaign process at " . $startTime->format('Y-m-d H:i:s'));
        $this->info("Batch size: {$this->batchSize}, Max concurrent: {$this->maxConcurrent}");

        try {
            // Optimasi: Load campaigns dengan pagination
            $campaigns = $this->getCampaignsToProcess();
            $this->info("Found " . $campaigns->count() . " campaigns to process");

            $mailService = new PepipostV5Service();
            
            // Pre-load excluded emails sekali saja
            $excludedEmails = $this->getExcludedEmails();
            
            foreach ($campaigns as $campaign) {
                if ($campaign->schedule) {
                    $campaignStartTime = Carbon::now();
                    $this->info("\nProcessing campaign: {$campaign->name} (ID: {$campaign->id})");
                    
                    // Initialize campaign stats
                    $this->campaignStats[$campaign->id] = [
                        'campaign_name' => $campaign->name,
                        'total_contacts' => 0,
                        'emails_sent' => 0,
                        'emails_failed' => 0,
                        'success_rate' => 0,
                        'start_time' => $campaignStartTime
                    ];
                    
                    $template = $campaign->template->first();
                    $campaignEmailsSent = 0;
                    
                    if ($campaign->type == 'external') {
                        $campaignEmailsSent = $this->processExternalContacts(
                            $campaign, $template, $mailService, $excludedEmails
                        );
                    } else {
                        $campaignEmailsSent = $this->processInternalContacts(
                            $campaign, $template, $mailService, $excludedEmails
                        );
                    }
                    
                    $campaignEndTime = Carbon::now();
                    
                    // Calculate success rate for this campaign
                    $totalContacts = $this->campaignStats[$campaign->id]['total_contacts'];
                    $successRate = $totalContacts > 0 ? round(($campaignEmailsSent / $totalContacts) * 100, 2) : 0;
                    $this->campaignStats[$campaign->id]['success_rate'] = $successRate;
                    $this->campaignStats[$campaign->id]['end_time'] = $campaignEndTime;
                    
                    // Flush remaining email history batch
                    $this->flushEmailHistoryBatch();
                    
                    $campaign->status = 'Sent';
                    $campaign->save();
                    $campaignProcessed++;
                    $emailsSent += $campaignEmailsSent;
                    
                    $this->info("Completed campaign {$campaign->name}: {$campaignEmailsSent}/{$totalContacts} emails sent (Success Rate: {$successRate}%)");
                    
                    // Report to Campaign Center (non-blocking)
                    $this->reportToCampaignCenter($campaign, $campaignEmailsSent, $campaignStartTime, $campaignEndTime, $successRate);
                }
            }

            $endTime = Carbon::now();
            $durationSeconds = $startTime->diffInSeconds($endTime);
            $durationMilliseconds = $startTime->diffInMilliseconds($endTime);

            $this->info("\nCampaign process completed at " . $endTime->format('Y-m-d H:i:s'));
            $this->info("Total campaigns processed: {$campaignProcessed}");
            $this->info("Total emails sent: {$emailsSent}");
            $this->info("Duration: {$durationSeconds} seconds (" . $durationMilliseconds . "ms)");

            // Display campaign statistics
            $this->displayCampaignStatistics();

            // Gunakan milliseconds untuk perhitungan yang lebih akurat
            $emailsPerSecond = $emailsSent > 0 && $durationMilliseconds > 0 
                ? round(($emailsSent * 1000) / $durationMilliseconds, 2) 
                : 0;
                
            $this->info("Average: {$emailsPerSecond} emails/second");

            if ($emailsSent > 0) {
                $this->logger->logCommand(
                    'campaign',
                    $startTime,
                    $endTime,
                    $this->signature,
                    'S',
                    [
                        'emails_sent' => $emailsSent,
                        'campaigns_processed' => $campaignProcessed,
                        'duration_seconds' => $durationSeconds,
                        'duration_milliseconds' => $durationMilliseconds,
                        'emails_per_second' => $emailsPerSecond,
                        'campaign_statistics' => $this->campaignStats
                    ]
                );
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->flushEmailHistoryBatch(); // Ensure any pending logs are saved
            $this->logger->logCommand(
                'campaign',
                $startTime,
                Carbon::now(),
                $this->signature,
                'F',
                ['error' => $e->getMessage(), 'campaign_statistics' => $this->campaignStats]
            );
            throw $e;
        }
    }

    /**
     * Report campaign completion to Campaign Center
     */
    private function reportToCampaignCenter($campaign, $actualSent, $startTime, $endTime, $successRate)
    {
        // Skip if no campaign_center_id
        if (!$campaign->campaign_center_id) {
            $this->info("⚠️  Campaign {$campaign->id} has no campaign_center_id, skipping Campaign Center report");
            return;
        }

        try {
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL');
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            $appCode = env('CAMPAIGN_CENTER_CODE', 'RRP');

            if (!$campaignCenterUrl || !$apiToken) {
                $this->warn("⚠️  Campaign Center URL or API token not configured, skipping report for campaign {$campaign->id}");
                return;
            }

            $requestData = [
                'app_code' => $appCode,
                'campaign_id' => $campaign->campaign_center_id,
                'actual_sent' => $actualSent,
                'completion_date' => $endTime->format('Y-m-d'),
                'completion_details' => [
                    'start_time' => $startTime->format('Y-m-d H:i:s'),
                    'end_time' => $endTime->format('Y-m-d H:i:s'),
                    'success_rate' => $successRate
                ]
            ];

            // Log the request for debugging
            Log::info('Campaign Center Completion Report', [
                'campaign_id' => $campaign->id,
                'campaign_center_id' => $campaign->campaign_center_id,
                'request_data' => $requestData
            ]);

            // Use synchronous HTTP request with short timeout
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])
            ->timeout(15) // Short timeout to avoid blocking too long
            ->retry(2, 500) // Retry 2 times with 500ms delay
            ->post($campaignCenterUrl . '/api/campaign/complete', $requestData);

            if ($response->successful()) {
                $this->info("✅ Campaign {$campaign->id} completion reported to Campaign Center successfully");
                Log::info('Campaign Center report successful', [
                    'campaign_id' => $campaign->id,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            } else {
                $this->warn("⚠️  Campaign Center report failed for campaign {$campaign->id}: HTTP {$response->status()}");
                Log::warning('Campaign Center report failed', [
                    'campaign_id' => $campaign->id,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            // Don't let Campaign Center reporting errors affect the main process
            $this->warn("⚠️  Failed to report campaign {$campaign->id} to Campaign Center: " . $e->getMessage());
            Log::error('Campaign Center reporting failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Optimasi: Load campaigns dengan lazy loading
     */
    private function getCampaignsToProcess()
    {
        return Campaign::where('status', '!=', 'Sent')
            ->with(['schedule' => function($q) {
                $q->where('schedule', '<=', date('Y-m-d H:i:s'));
            }])
            ->with(['template'])
            ->whereHas('schedule', function($q) {
                $q->where('schedule', '<=', date('Y-m-d H:i:s'));
            })
            ->get();
    }

    /**
     * Optimasi: Pre-load excluded emails sekali saja
     */
    private function getExcludedEmails(): array
    {
        $excluded = [];
        
        // Load excluded emails
        ExcludedEmail::chunk(1000, function($excludeds) use (&$excluded) {
            foreach ($excludeds as $item) {
                $excluded[] = $item->email;
            }
        });
        
        // Load unsubscribed emails
        EmailResponse::where('event', '=', 'unsubscribed')
            ->chunk(1000, function($responses) use (&$excluded) {
                foreach ($responses as $response) {
                    $excluded[] = $response->recepient;
                }
            });
        
        return array_unique($excluded);
    }

    /**
     * Process external contacts dengan batching dan concurrent processing
     */
    private function processExternalContacts($campaign, $template, $mailService, $excludedEmails): int
    {
        $emailsSent = 0;
        $tags = 'campaign,' . $campaign->id . ',' . $campaign->name . ',' . env('UNIT');
        
        // Load contacts dalam batch untuk menghindari memory issues
        $campaign->external()
            ->where('status', '=', 'queue')
            ->chunk($this->batchSize, function($contacts) use (
                $campaign, $template, $mailService, $excludedEmails, $tags, &$emailsSent
            ) {
                $emailsSent += $this->processBatchContacts(
                    $contacts, $campaign, $template, $mailService, $excludedEmails, $tags, 'external'
                );
            });
            
        return $emailsSent;
    }

    /**
     * Process internal contacts dengan batching dan concurrent processing
     */
    private function processInternalContacts($campaign, $template, $mailService, $excludedEmails): int
    {
        $emailsSent = 0;
        $tags = 'campaign,' . $campaign->id . ',' . $campaign->name . ',' . env('UNIT');
        
        // Load contacts dalam batch untuk menghindari memory issues
        $campaign->contact()
            ->where('status', '=', 'queue')
            ->chunk($this->batchSize, function($contacts) use (
                $campaign, $template, $mailService, $excludedEmails, $tags, &$emailsSent
            ) {
                $emailsSent += $this->processBatchContacts(
                    $contacts, $campaign, $template, $mailService, $excludedEmails, $tags, 'campaign'
                );
            });
            
        return $emailsSent;
    }

    /**
     * Process batch contacts dengan concurrent API calls
     */
    private function processBatchContacts(
        $contacts, $campaign, $template, $mailService, $excludedEmails, $tags, $type
    ): int {
        $emailsSent = 0;
        $validContacts = [];
        
        // Filter valid contacts
        foreach ($contacts as $contact) {
            if (!in_array($contact->email, $excludedEmails)) {
                $validContacts[] = $contact;
            }
        }
        
        if (empty($validContacts)) {
            return 0;
        }
        
        // Update total contacts for this campaign
        $this->campaignStats[$campaign->id]['total_contacts'] += count($validContacts);
        
        $this->info("Processing batch of " . count($validContacts) . " valid contacts");
        
        // Process dalam chunks untuk concurrent processing
        $chunks = array_chunk($validContacts, $this->maxConcurrent);
        
        foreach ($chunks as $chunk) {
            $promises = [];
            $chunkResults = [];
            
            // Simulate concurrent processing (dalam implementasi nyata, gunakan Guzzle promises atau ReactPHP)
            foreach ($chunk as $contact) {
                try {
                    $response = $mailService->send($contact, $template, $tags, $type, $campaign);
                    
                    // Update status
                    if ($type === 'external') {
                        $campaign->external()->updateExistingPivot($contact, ['status' => 'sent']);
                    } else {
                        $campaign->contact()->updateExistingPivot($contact, ['status' => 'sent']);
                    }
                    
                    // Batch email history logging
                    $this->addToEmailHistoryBatch($contact, $template, $tags, 'SendToPepipostV5', $campaign, null, $response);
                    
                    $emailsSent++;
                    $this->campaignStats[$campaign->id]['emails_sent']++;
                    $this->info("✅ Email sent to {$contact->email} - Message ID: {$response['message_id']}");
                    
                } catch (\Exception $e) {
                    $this->addToEmailHistoryBatch($contact, $template, $tags, 'FailToPepipostV5', $campaign, $e->getMessage());
                    $this->campaignStats[$campaign->id]['emails_failed']++;
                    $this->error("❌ Failed to send email to {$contact->email}: " . $e->getMessage());
                }
            }
            
            // Flush batch jika sudah mencapai batas
            if (count($this->emailHistoryBatch) >= 100) {
                $this->flushEmailHistoryBatch();
            }
            
            // Rate limiting - pause sebentar antar chunk
            if (count($chunks) > 1) {
                usleep(100000); // 100ms delay
            }
        }
        
        return $emailsSent;
    }

    /**
     * Display campaign statistics
     */
    private function displayCampaignStatistics()
    {
        if (empty($this->campaignStats)) {
            return;
        }

        $this->info("\n📊 Campaign Statistics:");
        $this->info("┌─────────────────────────────────────────────────────────────────────────────┐");
        $this->info("│ Campaign ID │ Campaign Name           │ Total │ Sent │ Failed │ Success Rate │");
        $this->info("├─────────────────────────────────────────────────────────────────────────────┤");
        
        foreach ($this->campaignStats as $campaignId => $stats) {
            $this->info(sprintf(
                "│ %-11s │ %-23s │ %-5d │ %-4d │ %-6d │ %-12s │",
                $campaignId,
                substr($stats['campaign_name'], 0, 23),
                $stats['total_contacts'],
                $stats['emails_sent'],
                $stats['emails_failed'],
                $stats['success_rate'] . '%'
            ));
        }
        
        $this->info("└─────────────────────────────────────────────────────────────────────────────┘");
    }

    /**
     * Optimasi: Batch email history logging
     */
    private function addToEmailHistoryBatch($contact, $template, $tags, $status, $campaign, $errorMessage = null, $response = null)
    {
        $data = [
            'contactid' => $contact->id ?? null,
            'fname' => $contact->fname ?? $contact->first_name ?? null,
            'lname' => $contact->lname ?? $contact->last_name ?? null,
            'email' => $contact->email,
            'template_id' => $template->id ?? null,
            'tags' => $tags,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        if ($errorMessage) {
            $data['error_message'] = $errorMessage;
        }
        
        if ($response && isset($response['message_id'])) {
            $data['message_id'] = $response['message_id'];
        }
        
        if (isset($contact->profilesfolio) && $contact->profilesfolio->isNotEmpty()) {
            $folio = $contact->profilesfolio->first();
            $data['folio_master'] = $folio->folio_master ?? null;
            $data['folio'] = $folio->folio ?? null;
            $data['dateci'] = $folio->dateci ?? null;
            $data['dateco'] = $folio->dateco ?? null;
        }
        
        $this->emailHistoryBatch[] = $data;
    }

    /**
     * Flush email history batch ke database
     */
    private function flushEmailHistoryBatch()
    {
        if (!empty($this->emailHistoryBatch)) {
            try {
                DB::table('email_history')->insert($this->emailHistoryBatch);
                $this->info("📝 Logged " . count($this->emailHistoryBatch) . " email history records");
                $this->emailHistoryBatch = [];
            } catch (\Exception $e) {
                $this->error("Failed to batch log email history: " . $e->getMessage());
            }
        }
    }
}
