<?php

namespace App\Services;

use App\Models\Configuration;
use App\Http\Controllers\EmailTemplateController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PepipostV5Service
{
    private $apiKey;
    private $baseUrl = 'https://emailapi.netcorecloud.net/v5/mail/send';
    
    public function __construct()
    {
        $this->apiKey = env('PEPIPOST_API_KEY');
    }
    
    /**
     * Convert string to proper case - sama seperti PepipostMail
     */
    public function convertstring($str)
    {
        $spl = explode(' ', $str);
        $frag = [];
        foreach ($spl as $s) {
            array_push($frag, ucfirst(strtolower($s)));
        }
        return implode(' ', $frag);
    }
    
    /**
     * Send email using V5 API - mengikuti signature dan logika PepipostMail->send()
     */
    public function send($user = null, $template, $tag = null, $type, $campaign = null, $registrationcode = null)
    {
        $tempcontroller = new EmailTemplateController();
        $config = Configuration::find(1);
        
        // Generate xApiheader sama seperti di PepipostMail
        if (!empty($campaign)) {
            $xApiheader = Carbon::parse($campaign->schedule->schedule)->format('YmdHis') . '_' . $user->email;
        } else {
            $xApiheader = $tempcontroller->randomstr();
        }
        
        // Prepare data sama seperti di PepipostMail
        if ($type == 'external') {
            $data = [
                'firstname' => $this->convertstring($user->fname),
                'lastname' => $this->convertstring($user->lname),
                'hotelname' => $config->hotel_name,
            ];
        } else {
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
        
        // Subject logic sama seperti di PepipostMail
        if ($type == 'poststay' || $type == 'missyou' || $type == 'campaign' || $type == 'external' || $type == 'testing' || $type == 'prestay') {
            $subject = $template->subject;
        } else {
            $subject = $template->subject . ' ' . $this->convertstring($user->fname) . ' ' . $this->convertstring($user->lname);
        }
        
        // Prepare V5 API payload
        $payload = [
            'from' => [
                'email' => $config->sender_email,
                'name' => $config->sender_name
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'html',
                    'value' => $template->parse($data)
                ]
            ],
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $user->email,
                            'name' => trim($this->convertstring($user->fname ?? '') . ' ' . $this->convertstring($user->lname ?? ''))
                        ]
                    ],
                    'headers' => [
                        'X-APIHEADER' => $xApiheader
                    ]
                ]
            ],
            'settings' => [
                'click_tracking' => true,
                'open_tracking' => true,
                'unsubscribe_tracking' => true
            ]
        ];
        
        // Add tags if provided
        if ($tag) {
            $payload['tags'] = explode(',', $tag);
        }
        
        // Make API call
        return $this->makeApiCall($payload);
    }
    
    /**
     * Make HTTP request to Pepipost V5 API
     */
    private function makeApiCall($payload)
    {
        $headers = [
            'api_key: ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        $curl = curl_init();
        
        $options = [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ];
        
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200 && $httpCode !== 202) {
            $errorMessage = 'HTTP ' . $httpCode;
            if (isset($result['message'])) {
                $errorMessage .= ': ' . $result['message'];
            } elseif (isset($result['error'])) {
                $errorMessage .= ': ' . $result['error'];
            }
            throw new \Exception('API Error - ' . $errorMessage);
        }
        
        return [
            'status' => 'success',
            'response' => $result,
            'http_code' => $httpCode,
            'message_id' => $result['data']['message_id'] ?? null // Extract message_id for easy access
        ];
    }
    
    /**
     * Send simple test email
     */
    public function sendTestEmail($toEmail, $subject = 'Test Email V5', $content = null)
    {
        try {
            $config = Configuration::first();
            
            $defaultContent = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Test Email Pepipost V5 API</h2>
                <p>Halo,</p>
                <p>Ini adalah email test yang dikirim menggunakan <strong>Pepipost V5 API</strong>.</p>
                <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;">
                    <h4 style="margin: 0 0 10px 0; color: #007bff;">Detail Pengiriman:</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><strong>Waktu:</strong> ' . now()->format('Y-m-d H:i:s') . '</li>
                        <li><strong>Hotel:</strong> ' . $config->hotel_name . '</li>
                        <li><strong>Pengirim:</strong> ' . $config->sender_name . '</li>
                        <li><strong>API Version:</strong> V5</li>
                    </ul>
                </div>
                <p>Jika Anda menerima email ini, berarti konfigurasi Pepipost V5 API sudah berjalan dengan baik.</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
                <p style="font-size: 12px; color: #666;">Email ini dikirim secara otomatis untuk testing purposes.</p>
            </div>
            ';
            
            $payload = [
                'from' => [
                    'email' => $config->sender_email,
                    'name' => $config->sender_name
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $content ?? $defaultContent
                    ]
                ],
                'personalizations' => [
                    [
                        'to' => [
                            [
                                'email' => $toEmail,
                                'name' => $toEmail
                            ]
                        ],
                        'headers' => [
                            'X-APIHEADER' => 'TEST_V5_' . time() . '_' . substr(md5(uniqid()), 0, 8)
                        ]
                    ]
                ],
                'settings' => [
                    'click_tracking' => true,
                    'open_tracking' => true,
                    'unsubscribe_tracking' => true
                ],
                'tags' => ['test-email-v5']
            ];
            
            return $this->makeApiCall($payload);
            
        } catch (\Exception $e) {
            Log::error('Error sending test email via Pepipost V5: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get email tracking logs using message_id for V5 API
     * Menggunakan endpoint yang sama seperti V2 karena V5 belum memiliki endpoint tracking terpisah
     */
    public function getEmailTrackingByMessageId($messageId, $email, $senderEmail)
    {
        try {
            // Menggunakan endpoint V2 untuk tracking karena V5 belum memiliki endpoint tracking terpisah
            $baseUrl = 'https://api.pepipost.com/v2/logs';
            
            // Parameter untuk pencarian berdasarkan message_id dan email
            $params = [
                'startdate' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'enddate' => Carbon::now()->format('Y-m-d'),
                'limit' => 100,
                'sort' => 'asc',
                'fromaddress' => $senderEmail,
                'email' => $email,
                'messageid' => $messageId
            ];
            
            $url = $baseUrl . '?' . http_build_query($params);
            
            $headers = [
                'api_key: ' . $this->apiKey,
                'Content-Type: application/json'
            ];
            
            $curl = curl_init();
            
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            ];
            
            curl_setopt_array($curl, $options);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);
            curl_close($curl);
            
            if ($err) {
                throw new \Exception('cURL Error: ' . $err);
            }
            
            if ($httpCode !== 200) {
                throw new \Exception('HTTP Error: ' . $httpCode);
            }
            
            return json_decode($response, true);
            
        } catch (\Exception $e) {
            Log::error('Error getting email tracking for message_id ' . $messageId . ': ' . $e->getMessage());
            throw $e;
        }
    }
}