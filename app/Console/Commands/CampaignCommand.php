<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Configuration;
use App\Http\Controllers\MailgunController;
use App\Http\Controllers\PepipostMail;
use App\Models\EmailResponse;
use App\Models\ExcludedEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;


class CampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Task';

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
        $campaigns=Campaign::with(['schedule'=>function($q){
            $q->where('schedule','<',date('Y-m-d H:i:s'));
        }])->with(['contact'=>function($q){
            $q->where('status','=','queue')->take(2500);
        }])->with(['external'=>function($q){
            $q->where('status','=','queue')->take(2500);
        }])->get();
        $mail=new PepipostMail();
        foreach ($campaigns as $campaign){
            if($campaign->schedule){
                $schedule=$campaign->schedule->schedule;
                if (Carbon::now()->format('Y-m-d H:i')>=Carbon::parse($schedule)->format('Y-m-d H:i')){
                    $recepient=[];
                    $excludeds=ExcludedEmail::all();
                    foreach ($excludeds as $excluded){
                        array_push($recepient,$excluded->email);
                    }
                    $response=EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
                    foreach ($response as $res){
                        array_push($recepient,$res->recepient);
                    }
                    $template=$campaign->template->first();
                    if($campaign->type=='external'){
                        foreach ($campaign->external as $contact){
                            if(!in_array($contact->email,$recepient)){
                                $tags='campaign,'.$campaign->id.','.$campaign->name.','.env('UNIT');
                                $mail->send($contact,$template,$tags,'external',$campaign);
                                $campaign->external()->updateExistingPivot($contact,['status'=>'sent']);
                            }
                        }
                        $campaign->status='Sent';
                        $campaign->save();
                    } else {
                       foreach ($campaign->contact as $contact){
                            if(!in_array($contact->email,$recepient)){
                                $tags='campaign,'.$campaign->id.','.$campaign->name.','.env('UNIT');
                                $mail->send($contact,$template,$tags,'campaign',$campaign);
                                $campaign->contact()->updateExistingPivot($contact,['status'=>'sent']);
                            }
                        }
                        $campaign->status='Sent';
                        $campaign->save();
                    }
                }
            }

        }
    }
}
