<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PepipostMail;

class QuotaSyncController extends Controller
{
    public function syncQuotaInitial(Request $request)
    {
        try {
            // Ambil data quota dari PepipostMail
            $pepipostMail = new PepipostMail();
            $quotaData = $pepipostMail->getEmailQuota();
            
            if (isset($quotaData['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get quota data: ' . $quotaData['error']
                ], 500);
            }
            
            // Ambil app_code dari .env
            $appCode = env('UNIT', 'RRP');
            
            // Ambil campaign center URL dan API token dari .env
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL', 'https://crm-campaign-center.adityaputra.co');
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            // Format data sesuai dengan struktur yang diminta
            $syncData = [
                'app_code' => $appCode,
                'quota_data' => [
                    'today_used' => $quotaData['quota_info']['today_quota']['used'] ?? 0,
                    'monthly_used' => $quotaData['quota_info']['quota_used'] ?? 0,
                    'billing_cycle' => [
                        'start' => $quotaData['period']['start'] ?? '',
                        'end' => $quotaData['period']['end'] ?? ''
                    ]
                ],
                'sync_type' => 'initial'
            ];
            
            // Kirim data ke campaign center dengan Bearer token
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiToken
                ])
                ->post($campaignCenterUrl . '/api/quota/sync', $syncData);
            
            if ($response->successful()) {
                Log::info('Quota sync successful', [
                    'app_code' => $appCode,
                    'sync_data' => $syncData,
                    'response' => $response->json()
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Quota data synced successfully',
                    'sync_data' => $syncData,
                    'campaign_center_response' => $response->json()
                ]);
            } else {
                Log::error('Quota sync failed', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'sync_data' => $syncData
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to sync quota data to campaign center',
                    'error_details' => [
                        'status_code' => $response->status(),
                        'response' => $response->body()
                    ]
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in syncQuotaInitial: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function testQuotaSync(Request $request)
    {
        try {
            // Method untuk testing manual - menampilkan data yang akan dikirim tanpa benar-benar mengirim
            $pepipostMail = new PepipostMail();
            $quotaData = $pepipostMail->getEmailQuota();
            
            if (isset($quotaData['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get quota data: ' . $quotaData['error']
                ], 500);
            }
            
            $appCode = env('UNIT', 'RRP');
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL', 'https://crm-campaign-center.adityaputra.co');
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            $syncData = [
                'app_code' => $appCode,
                'quota_data' => [
                    'today_used' => $quotaData['quota_info']['today_quota']['used'] ?? 0,
                    'monthly_used' => $quotaData['quota_info']['quota_used'] ?? 0,
                    'billing_cycle' => [
                        'start' => $quotaData['period']['start'] ?? '',
                        'end' => $quotaData['period']['end'] ?? ''
                    ]
                ],
                'sync_type' => 'initial'
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test data prepared successfully',
                'campaign_center_url' => $campaignCenterUrl . '/api/quota/sync',
                'api_token' => $apiToken ? 'Bearer ' . substr($apiToken, 0, 8) . '...' : 'Not configured',
                'sync_data' => $syncData,
                'raw_quota_data' => $quotaData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}