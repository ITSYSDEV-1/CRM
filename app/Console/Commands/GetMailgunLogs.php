<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Models\Contact;
use App\Models\ExcludedEmail;
use App\Http\Controllers\PepipostMail;
use App\Models\MissYou;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MailgunLogs;
use Illuminate\Support\Facades\DB;
use App\Models\Configuration;

class GetMailgunLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getmailgunlogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Mailgun Logs';

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

        $mail = new PepipostMail();
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
        $bday=Contact::has('profilesfolio','>',0)
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
        foreach ($contacts as $key => $contact) {
          $logs=$mail->getMailLogs($config->sender_email,$contact);
          if(!empty($logs['data'])){
          foreach ($logs['data'] as $log) {
              $str=$log['fromaddress'];
              $domains=substr($str, strpos($str, "@") + 1);
              $urls = [];
              $email_id = $log['trid'];
              $recipient = $log['rcptEmail'];
              $status = $log['status'];
              $url = '';
              $tag = '';
              $id=$log['xapiheader'];
              $remarks=$log['remarks'];
              $delivery_status = '';
              if ($status == 'dropped') {
                  $event = 'failed';
                  $delivery_status = $log['remarks'];
                  // ExcludedEmail::updateOrCreate(
                  //     ['email' => $recipient],
                  //     ['reason'=>'Recipient’s email address is listed on suppression list']
                  // );
              }elseif ($status == 'open'){
                  $event = 'opened';
              }elseif($status == 'click'){
                  foreach ($log['clicks'] as $clicks) {
                      array_push($urls, $clicks['link']);
                  }
                  $url = implode(';',$urls);
                  $event = 'clicked';
              }elseif ($status == 'hardbounce'  || $status == 'invalid') {
                  $delivery_status = $log['remarks'];
                  $event = 'failed';
                   ExcludedEmail::updateOrCreate(
                       ['email' => $recipient],
                       ['reason'=>'Invalid email / Hard bounce']
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
              $tags = $log['tags'][0];
              if($tags){
                  $tag=$tags;
              }else{
                  $tag='';
              }
              $time = Carbon::parse($log['deliveryTime'])->format('Y-m-d H:i:s');
              if (!in_array($recipient, ['sempowlajuz@gmail.com','danabala72@gmail.com', 'mudanakomang@hotmail.com', 'agussatria712@gmail.com', 'it.sysdev@rcoid.com'])) {
                if($domains==env('DOMAIN') && ($id !="" || $id!=NULL) && $log['tags'][1]==env('UNIT')) {
                    MailgunLogs::updateOrCreate(
                        ['email_id'=>$id,'recipient' => $recipient],
                        ['message_id' => $email_id, 'event' => $event, 'severity' => NULL, 'url' => $url, 'tags' => $tag, 'recipient' => $recipient, 'timestamp' => $time, 'delivery_status' => $delivery_status]
                    );
                }
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
