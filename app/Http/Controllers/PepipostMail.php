<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PepipostAPILib;;
use App\Models\Configuration;
use App\Http\Controllers\EmailTemplateController;


use PepipostAPILib\Models\EmailBody;
use PepipostAPILib\Models\From;
use PepipostAPILib\Models\Personalizations;
use PepipostAPILib\Models\Settings;
use PepipostAPILib\PepipostAPIClient;

class PepipostMail extends Controller
{
    //

    public function convertstring($str){
        $spl=explode(' ',$str);
        $frag=[];
        foreach ($spl as $s){
            array_push($frag,ucfirst(strtolower($s)));
        }
        return implode(' ',$frag);
    }
    public function sendCC($template,$tag,$recipient){
        $tempcontroller=new EmailTemplateController();
        $config=Configuration::find(1);
        $client=new PepipostAPIClient();
        $emailController=$client->getEmail();
        $apikey=env('PEPIPOST_API_KEY');
        $body = new EmailBody();
        $body->personalizations=[];
        $body->personalizations[0]=new Personalizations();
        $body->personalizations[0]->recipient=$recipient;
        $body->personalizations[0]->xApiheader=$tempcontroller->randomstr();
        $body->from =new From();
        $body->from->fromEmail=$config->sender_email;
        $body->from->fromName=$config->sender_name;


        $data = [
            'contact_id' => '{contact_id}',
            'firstname' => '{firstname}',
            'lastname' => '{lastname}',
            'title' => '{title}',
            'registrationcode' => '{registrationcode}',
            'promoprestay' => '{promoprestay}',
            'hotelname' => $config->hotel_name,
            'gmname' => $config->gm_name,
        ];
        $subject="##Email Delivery Notification## ".\Carbon\Carbon::now()->format('Y-m-d').' '.$template->subject;
        $body->subject=$subject;
        $body->content=$template->parse($data);


        $body->settings=new Settings();
        $body->settings->clicktrack=1;
        $body->settings->opentrack=1;
        $body->settings->unsubscribe=1;
        $body->tags=$tag;
        $emailController->createSendEmail($apikey,$body);
    }
    public function send($user=null,$template,$tag=null,$type,$campaign=null, $registrationcode=null, $promoprestay=null){
        $tempcontroller=new EmailTemplateController();
        $config=Configuration::find(1);
        $client=new PepipostAPILib\PepipostAPIClient();
        $emailController=$client->getEmail();
        $apikey=env('PEPIPOST_API_KEY');
        $body = new PepipostAPILib\Models\EmailBody();
        $body->personalizations=[];
        $body->personalizations[0]=new PepipostAPILib\Models\Personalizations();
        $body->personalizations[0]->recipient=$user->email;
        if(!empty($campaign)){
            $body->personalizations[0]->xApiheader=Carbon::parse($campaign->schedule->schedule)->format('YmdHis').'_'.$user->email;
        }else{
            $body->personalizations[0]->xApiheader=$tempcontroller->randomstr();
        }
        $body->from =new PepipostAPILib\Models\From();
        $body->from->fromEmail=$config->sender_email;
        $body->from->fromName=$config->sender_name;

        if($type=='external'){
            $data=[
                'firstname'=>$this->convertstring($user->fname) ,
                'lastname'=>$this->convertstring($user->lname) ,
                'hotelname' => $config->hotel_name,
            ];
        }else {
            $data = [
                'contact_id' => $user->contactid,
                'firstname' => $this->convertstring($user->fname),
                'lastname' => $this->convertstring($user->lname),
                'title' => $this->convertstring($user->salutation),
                'hotelname' => $config->hotel_name,
                'gmname' => $config->gm_name,
                'registrationcode' => $registrationcode,
                'promoprestay' => $promoprestay,
            ];
        }
        if($type=='poststay' || $type=='missyou' || $type=='campaign' || $type=='external' || $type=='testing' || $type=='prestay') {
            $subject = $template->subject;
        }else{
            $subject=$template->subject.' '.$this->convertstring($user->fname).' '.$this->convertstring($user->lname);
        }
        $body->subject=$subject;
        $body->content=$template->parse($data);
        $body->settings=new PepipostAPILib\Models\Settings();
        $body->settings->clicktrack=1;
        $body->settings->opentrack=1;
        $body->settings->unsubscribe=1;
        $body->tags=$tag;
        $emailController->createSendEmail($apikey,$body);
    }

    public function getLogs($sender,$contact){
      $base='https://api.pepipost.com/v2/logs?';
      $curl=curl_init();
      $date=\Carbon\Carbon::now()->subDays(15)->format('Y-m-d');
      $enddate=\Carbon\Carbon::now()->format('Y-m-d');
      $apikey=[
          "api_key:".env('PEPIPOST_API_KEY'),
      ];
      $opt=[
          CURLOPT_URL=>$base.'startdate='.$date.'&enddate='.$enddate.'&limit=1&sort=asc&fromaddress='.$sender.'&email='.$contact->email,
          CURLOPT_RETURNTRANSFER=>true,
          CURLOPT_ENCODING=>"",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER=>$apikey,
      ];
      curl_setopt_array($curl,$opt);
      $response=curl_exec($curl);
      return json_decode($response,true);
    }

    public function getMailLogs($sender){
        $base='https://api.pepipost.com/v2/logs?';
        $curl=curl_init();
        $date=\Carbon\Carbon::now()->subDays(15)->format('Y-m-d');
        $enddate=\Carbon\Carbon::now()->format('Y-m-d');
        $apikey=[
            "api_key:".env('PEPIPOST_API_KEY'),
        ];
        $opt=[
            CURLOPT_URL=>$base.'startdate='.$date.'&enddate='.$enddate.'&limit=100&sort=asc&fromaddress='.$sender,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_ENCODING=>"",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER=>$apikey,
        ];
        curl_setopt_array($curl,$opt);
        $response=curl_exec($curl);
        return json_decode($response,true);
    }
    public function getContactLog($contact,$sender){
        $base='https://api.pepipost.com/v2/logs?';
        $curl=curl_init();
        $date=\Carbon\Carbon::now()->subDays(15)->format('Y-m-d');
        $enddate=\Carbon\Carbon::now()->format('Y-m-d');
        $apikey=[
            "api_key:".env('PEPIPOST_API_KEY'),
        ];
        $opt=[
            CURLOPT_URL=>$base.'startdate='.$date.'&enddate='.$enddate.'&limit=1&sort=asc&fromaddress='.$sender.'&email='.$contact->email,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_ENCODING=>"",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER=>$apikey,
        ];
        curl_setopt_array($curl,$opt);
        $response=curl_exec($curl);
        return json_decode($response,true);
    }
    public function getCampaignLogs($contact,$date,$campaign){
        $config=Configuration::first();
        $sender=$config->sender_email;
        $startdate=\Carbon\Carbon::parse($date)->format('Y-m-d');
        $enddate=\Carbon\Carbon::parse($date)->addDays(15)->format('Y-m-d');
        $apikey=[
            "api_key:".env('PEPIPOST_API_KEY'),
        ];
        $xapiheader=\Carbon\Carbon::parse($campaign->schedule->schedule)->format('YmdHis').'_'.$contact->email;
        $base='https://api.pepipost.com/v2/logs?';
        $curl=curl_init();
        $opt=[
            CURLOPT_URL=>$base.'startdate='.$startdate.'&enddate='.$enddate.'&limit=1&email='.$contact->email.'&fromemail='.$sender.'&xapiheader='.$xapiheader,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_ENCODING=>"",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER=>$apikey,
        ];
        curl_setopt_array($curl,$opt);
        $response=curl_exec($curl);
        return json_decode($response,true);
    }
}
