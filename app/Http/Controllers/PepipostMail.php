<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PepipostAPILib;;
use App\Models\Configuration;
use App\Models\ExcludedEmail;
use App\Http\Controllers\EmailTemplateController;
use Illuminate\Support\Facades\Log;

use PepipostAPILib\Models\EmailBody;
use PepipostAPILib\Models\From;
use PepipostAPILib\Models\Personalizations;
use PepipostAPILib\Models\Settings;
use PepipostAPILib\PepipostAPIClient;

class PepipostMail extends Controller
{
    //
    public function getEmailQuota($requestedMonth = null)
    {
        try {

            $quotaConfig = [
                'period' => [
                    'total' => 150000,  // Total quota per periode billing
                ],
                'daily' => [
                    'limit' => 3000     // Batas maksimum pengiriman per hari
                ]
            ];

            $base = 'https://api.pepipost.com/v2/stats';
            $curl = curl_init();
            
            // Implementasi billing cycle dinamis
            $now = Carbon::now();
            
            // Jika bulan spesifik diminta
            if ($requestedMonth !== null) {
                $requestedMonth = (int)$requestedMonth;
                if ($requestedMonth >= 1 && $requestedMonth <= 12) {
                    $now = Carbon::create(null, $requestedMonth, 1);
                }
            }
            
            $currentDay = $now->day;
            $currentMonth = $now->month;
        
        // Fungsi helper untuk menentukan tanggal akhir billing
        $getEndDay = function($month) {
            // Bulan dengan 31 hari biasanya berakhir di 20
            if (in_array($month, [1,3,5,7,8,10,12])) {
                return 20;
            }
            // Februari special case
            else if ($month == 2) {
                return 19;
            }
            // Bulan dengan 30 hari berakhir di 19
            else {
                return 19;
            }
        };
        
        // Fungsi helper untuk menentukan tanggal mulai billing
        $getStartDay = function($month) {
            // Bulan sebelum Februari mulai tanggal 20
            if ($month == 1) {
                return 20;
            }
            // Bulan setelah Februari mulai tanggal 22
            else if ($month == 3) {
                return 22;
            }
            // Default mulai tanggal 21
            else {
                return 21;
            }
        };
        
          // Tentukan periode billing cycle
          if ($currentDay < $getStartDay($currentMonth)) {
            // Masih dalam cycle bulan sebelumnya
            $prevMonth = $now->copy()->subMonth();
            $startDate = $prevMonth->copy()->setDay($getStartDay($prevMonth->month))->format('Y-m-d');
            $endDate = $now->copy()->setDay($getEndDay($currentMonth))->format('Y-m-d');
        } else {
            // Cycle baru dimulai
            $nextMonth = $now->copy()->addMonth();
            $startDate = $now->copy()->setDay($getStartDay($currentMonth))->format('Y-m-d');
            $endDate = $nextMonth->copy()->setDay($getEndDay($nextMonth->month))->format('Y-m-d');
        }
        
        $base .= "?startdate={$startDate}&enddate={$endDate}";
        
        $apikey = [
            "api_key:" . env('PEPIPOST_API_KEY'),
            "Content-Type: application/json"
        ];
        
        $opt = [
            CURLOPT_URL => $base,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $apikey,
        ];
        
        curl_setopt_array($curl, $opt);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            Log::error('Error getting email quota: ' . $err);
            return ['error' => $err, 'data' => null];
        }
        
        $result = json_decode($response, true);
        
        
        // Hitung total metrics
        $totalMetrics = [
            'sent' => 0,
            'bounce' => 0,
            'open' => 0,
            'click' => 0,
            'spam' => 0,
            'invalid' => 0,
            'unsub' => 0,
            'dropped' => 0,
            'requests' => 0
        ];
        
        $dailyRequests = [];
        $todayRequests = 0;
        $today = $now->format('Y-m-d');
        
        if (isset($result['data'])) {
            foreach ($result['data'] as $dayStats) {
                if (isset($dayStats['stats'][0]['metrics'])) {
                    $metrics = $dayStats['stats'][0]['metrics'];
                    
                    // Hitung request untuk setiap hari
                    $dailyRequest = intval($metrics['sent']) + 
                                  intval($metrics['bounce']) + 
                                  intval($metrics['invalid']) +
                                  intval($metrics['dropped']);
                    
                    // Simpan request per hari
                    $dailyRequests[$dayStats['date']] = $dailyRequest;
                    
                    // Hitung request untuk hari ini
                    if ($dayStats['date'] === $today) {
                        $todayRequests = $dailyRequest;
                    }
                    
                    // Update total metrics
                    foreach ($metrics as $key => $value) {
                        if (isset($totalMetrics[$key])) {
                            $totalMetrics[$key] += intval($value);
                        }
                    }
                    
                    // Update total requests
                    $totalMetrics['requests'] += $dailyRequest;
                }
            }
        }
        
        $quotaInfo = [
            'total_quota' => $quotaConfig['period']['total'],
            'daily_quota' => $quotaConfig['daily']['limit'],
            'today_quota' => [
                'used' => $todayRequests,
                'remaining' => $quotaConfig['daily']['limit'] - $todayRequests,
                'usage_percentage' => ($todayRequests / $quotaConfig['daily']['limit']) * 100
            ],
            'billing_cycle' => [
                'start' => $startDate,
                'end' => $endDate,
                'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
                'current_period' => Carbon::parse($startDate)->format('F d, Y') . ' To ' . Carbon::parse($endDate)->format('F d, Y'),
                'days_remaining' => Carbon::parse($endDate)->diffInDays($now),
                'cycle_pattern' => [
                    'start_day' => $getStartDay($currentMonth),
                    'end_day' => $getEndDay($currentMonth),
                    'current_month' => $currentMonth
                ]
            ],
            'quota_used' => $totalMetrics['requests'],
            'quota_remaining' => $quotaConfig['period']['total'] - $totalMetrics['requests'],
            'quota_usage_percentage' => ($totalMetrics['requests'] / $quotaConfig['period']['total']) * 100,
            'average_daily_usage' => $totalMetrics['requests'] / 30,
            'daily_quota_remaining' => ($totalMetrics['requests'] > 0) ? 
                ($quotaConfig['period']['total'] - $totalMetrics['requests']) / Carbon::parse($endDate)->diffInDays($now) : 
                $quotaConfig['daily']['limit']
        ];
        
        return [
            'status' => 'success',
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'metrics' => $totalMetrics,
            'daily_requests' => $dailyRequests,
            'quota_info' => $quotaInfo,
            'raw_data' => $result
        ];
        
    } catch (\Exception $e) {
        Log::error('Exception in getEmailQuota: ' . $e->getMessage());
        return ['error' => $e->getMessage(), 'data' => null];
    }
}

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
    public function send($user=null,$template,$tag=null,$type,$campaign=null, $registrationcode=null){
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
                'registrationcode' => $registrationcode
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
    
    /**
     * Mengirim email sederhana tanpa template
     * 
     * @param string $fromEmail Email pengirim
     * @param string $toEmail Email penerima
     * @param string $subject Subject email
     * @param string $htmlContent Konten HTML email
     * @return array Hasil pengiriman email
     */
    public function sendSimpleEmail($fromEmail, $toEmail, $subject, $htmlContent)
    {
        try {
            $config = Configuration::first();
            $client = new PepipostAPILib\PepipostAPIClient();
            $emailController = $client->getEmail();
            $apikey = env('PEPIPOST_API_KEY');
                    // Log informasi pengirim untuk debugging
            Log::info('Sending email with:', [
                'from_email' => $fromEmail,
                'from_name' => $config->sender_name,
                'to_email' => $toEmail,
                'subject' => $subject,
                'api_key_exists' => !empty($apikey)
            ]);
            
            // Siapkan body email
            $body = new PepipostAPILib\Models\EmailBody();
            $body->personalizations = [];
            $body->personalizations[0] = new PepipostAPILib\Models\Personalizations();
            $body->personalizations[0]->recipient = $toEmail;
            
            // Generate random xApiheader
            $tempcontroller = new EmailTemplateController();
            $body->personalizations[0]->xApiheader = $tempcontroller->randomstr();
            
            // Set pengirim
            $body->from = new PepipostAPILib\Models\From();
            $body->from->fromEmail = $fromEmail;
            $body->from->fromName = $config->sender_name;
            
            // Set subject dan content
            $body->subject = $subject;
            $body->content = $htmlContent;
            
            // Set settings
            $body->settings = new PepipostAPILib\Models\Settings();
            $body->settings->clicktrack = 1;
            $body->settings->opentrack = 1;
            $body->settings->unsubscribe = 1;
            
            // Set tag
            $body->tags = "test-email";
            
            // Kirim email
            $response = $emailController->createSendEmail($apikey, $body);
            
            // Tambahkan log untuk debugging
            Log::info('Pepipost response:', ['response' => $response]);
            
            return [
                'status' => 'success',
                'response' => $response,
                'sender' => [
                    'email' => $fromEmail,
                    'name' => $config->sender_name
                ]
            ];
        } catch (\Exception $e) {
            // Log error
            Log::error('Error sending simple email: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
    
    /**
     * Mengirim email ulang tahun secara paksa ke alamat email tertentu
     * dan menyimpan log ke tabel mailgun_logs
     * 
     * @param string $toEmail Email penerima
     * @return array Hasil pengiriman email
     */
    public function forceSendBirthdayEmail($toEmail)
    {
        try {
            $config = Configuration::first();
            $client = new PepipostAPILib\PepipostAPIClient();
            $emailController = $client->getEmail();
            $apikey = env('PEPIPOST_API_KEY');
            
            // Log informasi pengirim untuk debugging
            Log::info('Sending forced birthday email to:', [
                'to_email' => $toEmail,
                'api_key_exists' => !empty($apikey)
            ]);
            
            // Cek apakah email ada di daftar excluded
            $recepient = [];
            $excludeds = ExcludedEmail::all();
            foreach ($excludeds as $excluded) {
                array_push($recepient, $excluded->email);
            }
            
            // Cek apakah email ada di daftar unsubscribed
            $response = \App\Models\EmailResponse::where('event', '=', 'unsubscribed')->select('recepient')->get();
            foreach ($response as $res) {
                array_push($recepient, $res->recepient);
            }
            
            // Jika email ada di daftar excluded atau unsubscribed, jangan kirim
            if (in_array($toEmail, $recepient)) {
                return [
                    'status' => 'error',
                    'message' => 'Email is excluded or unsubscribed'
                ];
            }
            
            // Buat data dummy untuk template
            $dummyUser = (object)[
                'contactid' => '12345',
                'fname' => 'Angga',
                'lname' => 'Putra',
                'salutation' => 'Mr.',
                'email' => $toEmail
            ];
            
            // Ambil template email ulang tahun
            $template = \App\Models\MailEditor::where('type', 'birthday')->first();
            
            if (!$template) {
                return [
                    'status' => 'error',
                    'message' => 'Template email ulang tahun tidak ditemukan'
                ];
            }
            
            // Siapkan body email
            $body = new PepipostAPILib\Models\EmailBody();
            $body->personalizations = [];
            $body->personalizations[0] = new PepipostAPILib\Models\Personalizations();
            $body->personalizations[0]->recipient = $toEmail;
            
            // Generate random xApiheader
            $tempcontroller = new EmailTemplateController();
            $xApiHeader = $tempcontroller->randomstr();
            $body->personalizations[0]->xApiheader = $xApiHeader;
            
            // Set pengirim
            $body->from = new PepipostAPILib\Models\From();
            $body->from->fromEmail = $config->sender_email;
            $body->from->fromName = $config->sender_name;
            
            // Siapkan data untuk template
            $data = [
                'contact_id' => $dummyUser->contactid,
                'firstname' => $this->convertstring($dummyUser->fname),
                'lastname' => $this->convertstring($dummyUser->lname),
                'title' => $this->convertstring($dummyUser->salutation),
                'hotelname' => $config->hotel_name,
                'gmname' => $config->gm_name,
                'registrationcode' => null
            ];
            
            // Set subject dan content - sesuai dengan format di MailgunController->bdayemail
            $subject = $template->subject.' '.$this->convertstring($dummyUser->salutation).' '.$this->convertstring($dummyUser->fname).' '.$this->convertstring($dummyUser->lname);
            $body->subject = $subject;
            $body->content = $template->parse($data);
            
            // Set settings
            $body->settings = new PepipostAPILib\Models\Settings();
            $body->settings->clicktrack = 1;
            $body->settings->opentrack = 1;
            $body->settings->unsubscribe = 1;
            
            // Set tag untuk tracking
            $tag = "test-birthday-email";
            $body->tags = $tag;
            
            // Kirim email
            $response = $emailController->createSendEmail($apikey, $body);
            
            // Tambahkan log untuk debugging
            Log::info('Pepipost birthday email response:', ['response' => $response]);
            
            // Simpan log ke tabel mailgun_logs
            $messageId = uniqid('pepi_', true);
            \App\Models\MailgunLogs::create([
                'event' => 'delivered',
                'recipient' => $toEmail,
                'sender' => $config->sender_email,
                'subject' => $subject,
                'message_id' => $messageId,
                'timestamp' => now()->timestamp,
                'tags' => $tag,
                'delivery_status' => 'delivered',
                'recipient_domain' => explode('@', $toEmail)[1],
                'xapiheader' => $xApiHeader
            ]);
            
            return [
                'status' => 'success',
                'response' => $response,
                'sender' => [
                    'email' => $config->sender_email,
                    'name' => $config->sender_name
                ],
                'message_id' => $messageId
            ];
        } catch (\Exception $e) {
            // Log error
            Log::error('Error sending forced birthday email: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
    
    /**
     * Mendapatkan log email dari API Pepipost untuk tanggal tertentu
     * 
     * @param string $sender Email pengirim
     * @param string $startdate Tanggal mulai (format Y-m-d)
     * @param string $enddate Tanggal akhir (format Y-m-d)
     * @return array Hasil log email
     */
    public function getMailLogsForDate($sender, $startdate, $enddate)
    {
        $base = 'https://api.pepipost.com/v2/logs?';
        $curl = curl_init();
        $apikey = [
            "api_key:" . env('PEPIPOST_API_KEY'),
        ];
        $opt = [
            CURLOPT_URL => $base . 'startdate=' . $startdate . '&enddate=' . $enddate . '&limit=1000&sort=asc&fromaddress=' . $sender,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $apikey,
        ];
        curl_setopt_array($curl, $opt);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            Log::error('Curl Error in getMailLogsForDate: ' . $err);
            return ['error' => $err, 'data' => []];
        }
        
        return json_decode($response, true);
    }
}
