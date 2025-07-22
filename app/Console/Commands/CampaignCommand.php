<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Configuration;
use App\Http\Controllers\MailgunController;
use App\Http\Controllers\PepipostMail;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use App\Services\SystemLogger;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CampaignCommand extends Command
{
    protected $signature = 'campaign';
    protected $description = 'Send Email Task';
    protected $logger;

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

        $this->info("Starting campaign process at " . $startTime->format('Y-m-d H:i:s'));

        try {
            $campaigns = Campaign::with(['schedule' => function($q) {
                $q->where('schedule', '<', date('Y-m-d H:i:s'));
            }])->with(['contact' => function($q) {
                $q->where('status', '=', 'queue')->take(2500);
            }])->with(['external' => function($q) {
                $q->where('status', '=', 'queue')->take(2500);
            }])->get();

            $this->info("Found " . $campaigns->count() . " campaigns to process");

            $mail = new PepipostMail();
            foreach ($campaigns as $campaign) {
                if ($campaign->schedule) {
                    $schedule = $campaign->schedule->schedule;
                    if (Carbon::now()->format('Y-m-d H:i') >= Carbon::parse($schedule)->format('Y-m-d H:i')) {
                        $this->info("\nProcessing campaign: {$campaign->name} (ID: {$campaign->id})");
                        $this->line("Type: {$campaign->type}");

                        $recepient = [];
                        $excludeds = ExcludedEmail::all();
                        foreach ($excludeds as $excluded) {
                            array_push($recepient, $excluded->email);
                        }
                        $response = EmailResponse::where('event', '=', 'unsubscribed')->select('recepient')->get();
                        foreach ($response as $res) {
                            array_push($recepient, $res->recepient);
                        }
                        $template = $campaign->template->first();

                        $campaignEmailsSent = 0;
                        if ($campaign->type == 'external') {
                            $this->line("Total external contacts to process: " . count($campaign->external));
                            foreach ($campaign->external as $contact) {
                                if (!in_array($contact->email, $recepient)) {
                                    $tags = 'campaign,' . $campaign->id . ',' . $campaign->name . ',' . env('UNIT');
                                    $mail->send($contact, $template, $tags, 'external', $campaign);
                                    $campaign->external()->updateExistingPivot($contact, ['status' => 'sent']);
                                    $emailsSent++;
                                    $campaignEmailsSent++;
                                }
                            }
                        } else {
                            $this->line("Total contacts to process: " . count($campaign->contact));
                            foreach ($campaign->contact as $contact) {
                                if (!in_array($contact->email, $recepient)) {
                                    $tags = 'campaign,' . $campaign->id . ',' . $campaign->name . ',' . env('UNIT');
                                    $mail->send($contact, $template, $tags, 'campaign', $campaign);
                                    $campaign->contact()->updateExistingPivot($contact, ['status' => 'sent']);
                                    $emailsSent++;
                                    $campaignEmailsSent++;
                                }
                            }
                        }
                        $campaign->status = 'Sent';
                        $campaign->save();
                        $campaignProcessed++;

                        $this->info("Completed campaign {$campaign->name}: {$campaignEmailsSent} emails sent");
                    }
                }
            }

            $endTime = Carbon::now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("\nCampaign process completed at " . $endTime->format('Y-m-d H:i:s'));
            $this->info("Total campaigns processed: {$campaignProcessed}");
            $this->info("Total emails sent: {$emailsSent}");
            $this->info("Duration: {$duration} seconds");

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
                        'duration_seconds' => $duration
                    ]
                );
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->logger->logCommand(
                'campaign',
                $startTime,
                Carbon::now(),
                $this->signature,
                'F',
                ['error' => $e->getMessage()]
            );
            throw $e;
        }
    }
}
