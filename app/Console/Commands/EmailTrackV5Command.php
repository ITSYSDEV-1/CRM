<?php

namespace App\Console\Commands;

use App\Services\PepipostV5Service;
use App\Models\Configuration;
use App\Models\ExcludedEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsSystemCommand;
use Carbon\Carbon;

class EmailTrackV5Command extends Command
{
    use LogsSystemCommand;

    protected $signature = 'emailtrack:v5
                          {--email= : Track specific email address}
                          {--days=7 : Number of days to look back for email history}
                          {--batch-size=50 : Number of emails to process per batch}';

    protected $description = 'Track email delivery status for PostStay V5 emails using message_id from email_history';

    private $delayBetweenEmails = 100000; // 0.1 seconds in microseconds

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->startLogging();
        
        try {
            $this->info('Starting EmailTrack V5 command...');
            
            // Get configuration
            $config = Configuration::first();
            if (!$config) {
                $this->error('Configuration not found');
                return 1;
            }
            
            // Fetch email history records to track
            $emailHistories = $this->fetchEmailHistories();
            
            if (empty($emailHistories)) {
                $this->info('No email histories to track.');
                $this->logCommandEnd('emailtrack:v5', 'No email histories found to track');
                return 0;
            }
            
            $this->info("Total email histories found: " . count($emailHistories));
            
            // Process in batches
            $this->processAllBatches($emailHistories, $config);
            
            $this->logCommandEnd('emailtrack:v5', 'Successfully processed all email tracking');
            
        } catch (\Exception $e) {
            Log::error('EmailTrack V5 command failed: ' . $e->getMessage());
            $this->error('Command failed: ' . $e->getMessage());
            
            $this->markFailed($e->getMessage());
            $this->logCommandEnd('emailtrack:v5');
            throw $e;
        }
    }

    private function fetchEmailHistories()
    {
        $days = (int) $this->option('days');
        $email = $this->option('email');
        
        $query = DB::table('email_history')
            ->where('tags', 'poststay-v5')
            ->where('status', 'SendToPepipostV5')
            ->whereNotNull('message_id')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('email_track')
                      ->whereRaw('email_track.email_history_id = email_history.id');
            });
        
        if ($email) {
            $query->where('email', $email);
        }
        
        return $query->get()->toArray();
    }

    private function processAllBatches($emailHistories, $config)
    {
        $batchSize = (int) $this->option('batch-size');
        $emailChunks = array_chunk($emailHistories, $batchSize);
        $totalBatches = count($emailChunks);
        
        $this->info(sprintf('Processing %d batches of %d emails each', $totalBatches, $batchSize));
        
        $processedCount = 0;
        $trackedCount = 0;
        $globalEmailIndex = 0;
        
        $pepipostService = new PepipostV5Service();
        
        foreach($emailChunks as $batchIndex => $emailBatch) {
            $currentBatch = $batchIndex + 1;
            
            $this->info(sprintf('\n=== Processing batch %d of %d (%d emails) ===', 
                $currentBatch, $totalBatches, count($emailBatch)));
            
            foreach($emailBatch as $emailHistory) {
                $globalEmailIndex++;
                $this->info(sprintf('Processing email %d of %d (%s)', 
                    $globalEmailIndex, count($emailHistories), $emailHistory->email));
                
                $this->processEmailTracking($emailHistory, $pepipostService, $config, $processedCount, $trackedCount);
                
                // Rate limiting
                usleep($this->delayBetweenEmails);
            }
            
            $this->info("Completed batch {$currentBatch}");
            
            // Delay between batches
            if($currentBatch < $totalBatches) {
                $this->info(sprintf('Batch %d completed. Waiting 5 seconds before next batch...', $currentBatch));
                sleep(5);
            }
        }
        
        $this->info(sprintf('\n=== COMPLETED ==='));
        $this->info(sprintf('Processed %d emails in %d batches', $processedCount, $totalBatches));
        $this->info(sprintf('Successfully tracked: %d emails', $trackedCount));
    }

    private function processEmailTracking($emailHistory, $pepipostService, $config, &$processedCount, &$trackedCount)
    {
        try {
            $this->info("Tracking message_id: {$emailHistory->message_id} for {$emailHistory->email}");
            
            $logs = $pepipostService->getEmailTrackingByMessageId(
                $emailHistory->message_id, 
                $emailHistory->email, 
                $config->sender_email
            );
            
            // Debug: tampilkan response
            $this->info("API Response: " . json_encode($logs));
            
            if (!is_null($logs) && !empty($logs['data'])) {
                foreach ($logs['data'] as $logEntry) {
                    // Pastikan ini adalah email yang benar
                    if ($logEntry['rcptEmail'] === $emailHistory->email && 
                        $logEntry['trid'] === $emailHistory->message_id) {
                        
                        $this->saveEmailTrack($emailHistory, $logEntry);
                        $trackedCount++;
                        
                        $this->info("Tracked event '{$logEntry['status']}' for {$emailHistory->email}");
                    }
                }
            } else {
                $this->info("No tracking data found for {$emailHistory->email}");
            }
            
            $processedCount++;
            
        } catch (\Exception $e) {
            Log::error("Failed to track email {$emailHistory->email}: " . $e->getMessage());
            $this->error("Failed to track email {$emailHistory->email}: " . $e->getMessage());
        }
    }

    private function saveEmailTrack($emailHistory, $logEntry)
    {
        $event = $this->mapStatusToEvent($logEntry['status']);
        $deliveryStatus = $logEntry['remarks'] ?? null;
        $urls = [];
        $url = '';
        
        // Handle clicks
        if ($logEntry['status'] == 'click' && isset($logEntry['clicks'])) {
            foreach ($logEntry['clicks'] as $click) {
                $urls[] = $click['link'];
            }
            $url = implode(';', $urls);
        }
        
        // Handle exclusions
        if (in_array($logEntry['status'], ['dropped', 'hardbounce', 'invalid', 'unsubscribe'])) {
            ExcludedEmail::updateOrCreate(
                ['email' => $emailHistory->email],
                ['reason' => $deliveryStatus ?: $logEntry['status']]
            );
        }
        
        $timestamp = Carbon::parse($logEntry['requestedTime'])->format('Y-m-d H:i:s');
        
        // Save to email_track table
        DB::table('email_track')->insert([
            'email_history_id' => $emailHistory->id,
            'message_id' => $logEntry['trid'],
            'email' => $logEntry['rcptEmail'],
            'event' => $event,
            'timestamp' => $timestamp,
            'url' => $url,
            'delivery_status' => $deliveryStatus,
            'severity' => null,
            'tags' => isset($logEntry['tags'][0]) ? $logEntry['tags'][0] : 'poststay-v5',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function mapStatusToEvent($status)
    {
        $statusMap = [
            'dropped' => 'failed',
            'open' => 'opened',
            'click' => 'clicked',
            'hardbounce' => 'failed',
            'invalid' => 'failed',
            'spam' => 'spam',
            'unsubscribe' => 'unsubscribed',
            'delivered' => 'delivered',
            'processed' => 'delivered'
        ];
        
        return $statusMap[$status] ?? 'delivered';
    }
}