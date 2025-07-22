<?php

namespace App\Console\Commands;

use App\Http\Controllers\PepipostMail;
use App\Models\Campaign;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use App\Traits\LogsSystemCommand;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncEmailResponse extends Command
{
    use LogsSystemCommand;

    protected $signature = 'syncemailresponse {--batch-size=10 : Number of campaigns to process per batch}';
    protected $description = 'Sync Email Response with batch processing';

    private $delayBetweenContacts = 100000; // 0.1 seconds in microseconds
    private $delayBetweenCampaigns = 200000; // 0.2 seconds in microseconds

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->startLogging();
        $this->info('Starting email response synchronization...');

        try {
            $this->info('Fetching campaigns from last 15 days...');
            $campaigns = Campaign::whereHas('schedule', function ($q) {
                return $q->where('schedule', '>=', Carbon::now()->subDays(15)->format('Y-m-d H:i:s'));
            })->with('schedule')->with('contact')->with('external')->get();

            if ($campaigns->isEmpty()) {
                $this->info('No campaigns found to process.');
                $this->logCommandEnd('syncemailresponse', 'No campaigns found');
                return Command::SUCCESS;
            }

            // Process campaigns in batches
            $this->processCampaignsInBatches($campaigns);

        } catch (\Exception $e) {
            $this->error("Error occurred: {$e->getMessage()}");
            $this->markFailed($e->getMessage());
            $this->addLogContext('exception', $e->getMessage());
            $this->logCommandEnd('syncemailresponse', 'Failed due to exception');
            return Command::FAILURE;
        }

        $this->logCommandEnd('syncemailresponse', 'Sync email response completed successfully');
        return Command::SUCCESS;
    }

    private function processCampaignsInBatches($campaigns)
    {
        $batchSize = (int) $this->option('batch-size');
        $campaignChunks = $campaigns->chunk($batchSize);
        $totalBatches = $campaignChunks->count();
        $totalCampaigns = $campaigns->count();
        $totalProcessed = 0;

        $this->info("Found {$totalCampaigns} campaigns to process in {$totalBatches} batches");

        foreach ($campaignChunks as $batchIndex => $campaignBatch) {
            $currentBatch = $batchIndex + 1;
            
            $this->info("\n=== Processing batch {$currentBatch} of {$totalBatches} ({$campaignBatch->count()} campaigns) ===");

            foreach ($campaignBatch as $campaignIndex => $campaign) {
                $globalCampaignNumber = ($batchIndex * $batchSize) + $campaignIndex + 1;
                $this->info("Processing campaign {$globalCampaignNumber} of {$totalCampaigns}: {$campaign->name}");

                $contactsProcessed = $this->processSingleCampaign($campaign);
                $totalProcessed += $contactsProcessed;

                // Delay between campaigns to reduce memory usage and API load
                usleep($this->delayBetweenCampaigns);
            }

            $this->info("Completed batch {$currentBatch}");
            
            // Longer delay between batches for memory cleanup
            if ($currentBatch < $totalBatches) {
                $this->info("Batch {$currentBatch} completed. Waiting 5 seconds before next batch...");
                sleep(5);
            }
        }

        $this->addLogContext('total_campaigns', $totalCampaigns);
        $this->addLogContext('total_contacts_processed', $totalProcessed);
        $this->info("\n=== SYNC COMPLETED ===");
        $this->info("Total campaigns processed: {$totalCampaigns}");
        $this->info("Total contacts processed: {$totalProcessed}");
    }

    private function processSingleCampaign($campaign)
    {
        $pp = new PepipostMail();
        $start = Carbon::parse($campaign->schedule->schedule);
        $end = Carbon::parse($campaign->schedule->schedule)->addDays(14);
        $now = Carbon::now();
        $contacts = [];
        $contactsProcessed = 0;

        // Collect all contacts (ORIGINAL LOGIC)
        if (!empty($campaign->contact)) {
            foreach ($campaign->contact as $c) {
                $contacts[] = $c;
            }
        }

        if (!empty($campaign->external)) {
            foreach ($campaign->external as $e) {
                $contacts[] = $e;
            }
        }

        $this->info("Found " . count($contacts) . " contacts for campaign {$campaign->name}");

        // Process each contact (ORIGINAL LOGIC PRESERVED)
        foreach ($contacts as $contactIndex => $contact) {
            $this->info("Processing contact " . ($contactIndex + 1) . " of " . count($contacts));
            
            $lg = $pp->getCampaignLogs($contact, $start->format('Y-m-d'), $campaign);

            if (!is_null($lg) && !empty($lg['data'])) {
                $log = $lg['data'][0];

                if ($log['tags'][0] == 'campaign' && $log['tags'][3] == env('UNIT')) {
                    if ($log['status'] == 'click') {
                        $urls = [];
                        foreach ($log['clicks'] as $cl) {
                            $urls[] = $cl['link'];
                        }
                        $url = implode(';', $urls);
                    } else {
                        $url = '';
                    }

                    EmailResponse::updateOrCreate(
                        ['email_id' => $log['xapiheader']],
                        [
                            'campaign_id' => $log['tags'][1],
                            'event' => $log['status'],
                            'url' => $url,
                            'tags' => $log['tags'][2],
                            'recepient' => $log['rcptEmail']
                        ]
                    );

                    if (in_array($log['status'], ['unsubscribe', 'dropped', 'hardbounce'])) {
                        ExcludedEmail::updateOrCreate(
                            ['email' => $log['rcptEmail']],
                            ['reason' => $log['remarks']]
                        );
                        $this->info("Email {$log['rcptEmail']} added to exclusion list due to {$log['status']}");
                    }
                    $contactsProcessed++;
                }
            }

            // Delay between contacts to reduce memory usage and API rate limiting
            usleep($this->delayBetweenContacts);
        }

        $this->info("Completed processing campaign {$campaign->name}\n");
        return $contactsProcessed;
    }
}
