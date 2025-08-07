<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\CampaignReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CampaignReservationController extends Controller
{
    public function requestSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required|exists:campaigns,id',
            'scheduled_date' => 'required|date|after:today',
            'email_count' => 'required|integer|min:1',
            'campaign_type' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
              $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . '/api/schedule/request';
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');

            $requestData = [
                'app_code' => 'RRP',
                'scheduled_date' => $request->scheduled_date,
                'email_count' => $request->email_count,
                'campaign_type' => $request->campaign_type,
                'subject' => $request->subject,
                'description' => $request->description
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->post($campaignCenterUrl, $requestData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Simpan response ke database
                $this->saveReservationResponse($request->campaign_id, $requestData, $responseData);
                
                return response()->json([
                    'success' => true,
                    'data' => $responseData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to campaign center',
                    'error' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error connecting to campaign center: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveReservationResponse($campaignId, $requestData, $responseData)
    {
        try {
            DB::beginTransaction();

            $reservation = CampaignReservation::create([
                'campaign_id' => $campaignId,
                'request_data' => json_encode($requestData),
                'response_data' => json_encode($responseData),
                'response_type' => $responseData['type'] ?? null,
                'success' => $responseData['success'] ?? false,
                'message' => $responseData['message'] ?? null,
                'original_date' => $requestData['scheduled_date'],
                'email_count_requested' => $requestData['email_count'],
                'quota_reserved' => $responseData['data']['quota_reserved'] ?? false,
                'created_at' => Carbon::now()
            ]);

            // Handle different response types
            if (isset($responseData['data'])) {
                $data = $responseData['data'];
                
                switch ($responseData['type']) {
                    case 'full_approval':
                        $this->handleFullApproval($reservation->id, $data);
                        break;
                    case 'full_auto_booking':
                        $this->handleFullAutoBooking($reservation->id, $data);
                        break;
                    case 'partial_approval_with_auto_booking':
                        $this->handlePartialApprovalWithAutoBooking($reservation->id, $data);
                        break;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function handleFullApproval($reservationId, $data)
    {
        DB::table('campaign_reservation_details')->insert([
            'reservation_id' => $reservationId,
            'campaign_center_id' => $data['campaign_id'],
            'scheduled_date' => $data['scheduled_date'],
            'email_count' => $data['email_count'],
            'status' => $data['status'],
            'is_main_campaign' => true,
            'created_at' => Carbon::now()
        ]);
    }

    private function handleFullAutoBooking($reservationId, $data)
    {
        foreach ($data['auto_booked_campaigns'] as $campaign) {
            DB::table('campaign_reservation_details')->insert([
                'reservation_id' => $reservationId,
                'campaign_center_id' => $campaign['campaign_id'],
                'scheduled_date' => $campaign['date'],
                'email_count' => $campaign['email_count'],
                'day_name' => $campaign['day_name'],
                'status' => 'auto_booked',
                'is_main_campaign' => false,
                'created_at' => Carbon::now()
            ]);
        }
    }

    private function handlePartialApprovalWithAutoBooking($reservationId, $data)
    {
        // Main campaign
        if (isset($data['main_campaign'])) {
            $main = $data['main_campaign'];
            DB::table('campaign_reservation_details')->insert([
                'reservation_id' => $reservationId,
                'campaign_center_id' => $main['campaign_id'],
                'scheduled_date' => $main['scheduled_date'],
                'email_count' => $main['email_count'],
                'status' => $main['status'],
                'is_main_campaign' => true,
                'created_at' => Carbon::now()
            ]);
        }

        // Auto booked campaigns
        if (isset($data['auto_booked_campaigns'])) {
            foreach ($data['auto_booked_campaigns'] as $campaign) {
                DB::table('campaign_reservation_details')->insert([
                    'reservation_id' => $reservationId,
                    'campaign_center_id' => $campaign['campaign_id'],
                    'scheduled_date' => $campaign['date'],
                    'email_count' => $campaign['email_count'],
                    'day_name' => $campaign['day_name'],
                    'status' => 'auto_booked',
                    'is_main_campaign' => false,
                    'created_at' => Carbon::now()
                ]);
            }
        }
    }

    public function getReservationHistory($campaignId)
    {
        $reservations = CampaignReservation::with('details')
            ->where('campaign_id', $campaignId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }
}