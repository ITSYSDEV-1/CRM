<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CampaignCalendarController extends Controller
{
    public function index()
    {
        return view('campaign.calendernew');
    }

    public function getMonthlyCalendar(Request $request)
    {
        try {
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);
            $appCode = env('CAMPAIGN_CENTER_CODE', 'RRP');
            
            // Add debugging
            Log::info("Calendar Request - Year: {$year}, Month: {$month}");
            
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . "/api/schedule/calendar/{$year}/{$month}";
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            Log::info("Campaign Center URL: {$campaignCenterUrl}");
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($campaignCenterUrl, [
                'app_code' => $appCode
            ]);
            
            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("API Response Data:", $responseData);
                return response()->json($responseData);
            } else {
                Log::error("API Error - Status: {$response->status()}, Body: {$response->body()}");
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch calendar data from Campaign Center'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Monthly Calendar Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching monthly calendar data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOverview(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            $appCode = env('CAMPAIGN_CENTER_CODE', 'RRP');
            
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . '/api/schedule/overview';
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($campaignCenterUrl, [
                'date' => $date,
                'app_code' => $appCode
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data from Campaign Center'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Calendar Overview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching calendar data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRangeOverview(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->addDays(30)->format('Y-m-d'));
            $appCode = env('CAMPAIGN_CENTER_CODE', 'RRP');
            
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . '/api/schedule/overview/range';
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->get($campaignCenterUrl, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'app_code' => $appCode
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch range data from Campaign Center'
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Calendar Range Overview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching calendar range data: ' . $e->getMessage()
            ], 500);
        }
    }
}