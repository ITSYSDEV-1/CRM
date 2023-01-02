<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Models\Contact;
use App\Models\MissYou;
use Illuminate\Console\Command;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailgunLogs;
use Illuminate\Support\Facades\DB;
use App\Models\Configuration;

class EmailLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emaillog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
        $pepi= new PepipostMail();
        $config=Configuration::first();

        $missyouconfig=MissYou::first();
        $bdayconfig=Birthday::first();

        $contacts=[];
        $poststay=Contact::has('profilesfolio','>',0)->whereHas('profilesfolio',function($q){
            return $q->whereDate('dateco','>=',\Carbon\Carbon::now()->subDays(15)->format('Y-m-d'))
                ->whereDate('dateco','<=',\Carbon\Carbon::now()->format('Y-m-d'));
        })->get();
        $missyou=Contact::has('profilesfolio','>',0)->whereHas('profilesfolio',function($q) use ($missyouconfig){
            return $q->whereDate('dateco','>=',\Carbon\Carbon::now()->subMonths($missyouconfig->sendafter)->subDays(15)->format('Y-m-d'))
                ->whereDate('dateco','<=',\Carbon\Carbon::now()->subMonths($missyouconfig->sendafter)->format('Y-m-d'));
        })->get();
        $bday=Contact::has('transaction','>',0)
            ->where(DB::raw('DATE_FORMAT(birthday,\'%m-%d\')'),'>=',\Carbon\Carbon::now()->addDays(abs($bdayconfig->sendafter))->subDays(15)->format('m-d'))
            ->where(DB::raw('DATE_FORMAT(birthday,\'%m-%d\')'),'<=',\Carbon\Carbon::now()->addDays(abs($bdayconfig->sendafter))->format('m-d'))
            ->get();

        foreach ($poststay as $key => $contact) {
            array_push($contacts, $contact);
        }
        foreach ($missyou as $key => $contact) {
            array_push($contacts, $contact);
        }
        foreach ($bday as $key => $contact) {
            array_push($contacts, $contact);
        }

        foreach($contacts as $key=>$contact){
            $logs=$pepi->getContactLog($contact,$config->sender_email);
            if(!is_null($logs) && !empty($logs['data'])){
                $data=$logs['data'];
                if($data[0]['tags'][0]=='poststay' || $data[0]['tags'][0]=='birthday' || $data[0]['tags'][0]=='missyou' || $data[0]['tags'][0]=='prestay'){
                    $email_id = $data[0]['trid'];
                    $recipient = $data[0]['rcptEmail'];
                    $status = $data[0]['status'];
                    $xapiheader=$data[0]['xapiheader'];
                    $urls=[];
                    $url='';
                    $delivery_status = '';
                    $tag=$data[0]['tags'][0];
                    if ($status == 'dropped') {
                        $event = 'failed';
                        $delivery_status = $data[0]['remarks'];
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>$delivery_status]
                        );
                    }elseif ($status == 'open'){
                        $event = 'opened';
                    }elseif($status == 'click'){
                        foreach ($data[0]['clicks'] as $clicks) {
                            array_push($urls, $clicks['link']);
                        }
                        $url = implode(';',$urls);
                        $event = 'clicked';
                    }elseif ($status == 'hardbounce'  || $status == 'invalid') {
                        $delivery_status = $data[0]['remarks'];
                        $event = 'failed';
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>$delivery_status]
                        );
                    }elseif ($status=='spam') {
                        $event = 'spam';
                    }elseif ($status=='unsubscribe'){
                        $event='unsubscribed';
                        ExcludedEmail::updateOrCreate(
                            ['email' => $recipient],
                            ['reason'=>'The recipient opted out using unsubscribe link']
                        );
                    }else{
                        $event = 'delivered';

                    }
                    $time = \Carbon\Carbon::parse($data[0]['requestedTime'])->format('Y-m-d H:i:s');
                    if($data[0]['tags'][1]==env('UNIT')) {
                        MailgunLogs::updateOrCreate(
                            ['email_id'=> $xapiheader,'recipient' => $recipient],
                            ['message_id' => $email_id, 'event' => $event, 'severity' => NULL, 'url' => $url, 'tags' => $tag, 'recipient' => $recipient, 'timestamp' => $time, 'delivery_status' => $delivery_status]
                        );
                    }
                    $mailgunlogs=MailgunLogs::whereIn('event',['failed','unsubscribed'])->get();

                    foreach ($mailgunlogs as $mailgunlog){
                        if($mailgunlog->event=='unsubscribed'){
                            ExcludedEmail::updateOrCreate(
                                ['email' => $mailgunlog->recipient],
                                ['reason'=>'The recipient opted out using unsubscribe link']
                            );
                        }else{
                            ExcludedEmail::updateOrCreate(
                                ['email' => $mailgunlog->recipient],
                                ['reason'=>$mailgunlog->delivery_status]
                            );
                        }
                    }
                }
            }
        }
    }
}
