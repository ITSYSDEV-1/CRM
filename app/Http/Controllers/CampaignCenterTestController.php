<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CampaignCenterTestController extends Controller
{
    public function pingTest(Request $request)
    {
        try {
            // Ambil campaign center URL dan API token dari .env
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL', 'http://jalak.campaign-center.com');
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            if (!$apiToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'CAMPAIGN_CENTER_API_TOKEN not configured in .env file'
                ], 500);
            }
            
            // Kirim ping request ke campaign center dengan Bearer token
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiToken
                ])
                ->get($campaignCenterUrl . '/api/ping');
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Campaign Center ping test successful', [
                    'url' => $campaignCenterUrl . '/api/ping',
                    'response' => $responseData
                ]);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ping test successful',
                    'campaign_center_url' => $campaignCenterUrl . '/api/ping',
                    'api_token_status' => 'Bearer token configured',
                    'campaign_center_response' => $responseData
                ]);
            } else {
                Log::error('Campaign Center ping test failed', [
                    'url' => $campaignCenterUrl . '/api/ping',
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ping test failed',
                    'error_details' => [
                        'status_code' => $response->status(),
                        'response' => $response->body()
                    ]
                ], $response->status());
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in Campaign Center ping test: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function testConnection(Request $request)
    {
        try {
            // Method untuk testing koneksi tanpa benar-benar mengirim request
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL', 'http://jalak.campaign-center.com');
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Connection test data prepared',
                'campaign_center_url' => $campaignCenterUrl . '/api/ping',
                'api_token_status' => $apiToken ? 'Bearer token configured (masked: ' . substr($apiToken, 0, 8) . '...)' : 'Not configured',
                'headers_to_send' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer [MASKED]'
                ],
                'expected_response_format' => [
                    'message' => 'pong',
                    'timestamp' => '2025-07-27T17:56:00.652880Z',
                    'version' => '1.0.0'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}