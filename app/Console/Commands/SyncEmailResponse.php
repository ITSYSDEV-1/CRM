<?php

namespace App\Console\Commands;

use App\Http\Controllers\PepipostMail;
use App\Models\Campaign;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncEmailResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncemailresponse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Email Response';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $campaigns=Campaign::whereHas('schedule',function($q){
            return $q->where('schedule','>=',Carbon::now()->subDays(15)->format('Y-m-d H:i:s'));
        })->with('schedule')->with('contact')->with('external')->get();
        $pp=new PepipostMail();
        foreach ($campaigns as $campaign) {
            $start = Carbon::parse($campaign->schedule->schedule);
            $end = Carbon::parse($campaign->schedule->schedule)->addDays(14);
            $now = Carbon::now();
            $contacts=[];
            if(!empty($campaign->contact)){
                foreach($campaign->contact as $c){
                    array_push($contacts,$c);
                }
            }
            if(!empty($campaign->external)){
                foreach($campaign->external as $e){
                    array_push($contacts,$e);
                }
            }
                foreach ($contacts as $contact) {
                        $lg = $pp->getCampaignLogs($contact, $start->format('Y-m-d'),$campaign);
                        if(!is_null($lg) && !empty($lg['data'])){
                        $log=$lg['data'][0];
                        if ($log['tags'][0] == 'campaign' && $log['tags'][3]==env('UNIT')) {
                            if ($log['status'] == 'click') {
                                $urls=[];
                                foreach ($log['clicks'] as $cl) {
                                    array_push($urls,$cl['link']);
                                }
                                $url = implode(';', $urls);
                            } else {
                                $url = '';
                            }
                            EmailResponse::updateOrCreate(
                                ['email_id' => $log['xapiheader']],
                                ['campaign_id' => $log['tags'][1], 'event' => $log['status'], 'url' => $url, 'tags' => $log['tags'][2], 'recepient' => $log['rcptEmail']]
                            );
                            if ($log['status'] == 'unsubscribe' || $log['status']=='dropped' || $log['status']=='hardbounce') {
                                ExcludedEmail::updateOrCreate(
                                    ['email' => $log['rcptEmail']],
                                    ['reason'=>$log['remarks']]
                                );
                            }
                        }
                    }
                }
        }
    }
}
