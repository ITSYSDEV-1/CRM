<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ProfileFolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BirthdayApiController extends Controller
{
    public function getBirthdayData(Request $request)
    {
        // Cek apakah fitur birthday push diaktifkan
        if (!env('BIRTHDAY_PUSH_ENABLED', false)) {
            return response()->json([
                'error' => 'Birthday feature is disabled',
                'message' => 'Birthday push feature is currently disabled in system configuration'
            ], 503);
        }

        try {
            // Validate API token
            $token = $request->bearerToken() ?? $request->get('token');
            if (!$this->validateApiToken($token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Get parameters
            $days = $request->get('days', 7); // Default 7 days
            $includeToday = $request->get('include_today', true);
            
            // Get birthday data
            $birthdayGuests = $this->getInHouseBirthdayGuests($days, $includeToday);
            
            $data = $this->formatBirthdayData($birthdayGuests);
            
            Log::info('Birthday API request', [
                'requester_ip' => $request->ip(),
                'days' => $days,
                'total_records' => count($data),
                'user_agent' => $request->userAgent()
            ]);
            
            return response()->json([
                'success' => true,
                'timestamp' => now()->toISOString(),
                'total_records' => count($data),
                'parameters' => [
                    'days_ahead' => $days,
                    'include_today' => $includeToday
                ],
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Birthday API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    private function validateApiToken($token)
    {
        $validToken = env('HOTSPOT_API_TOKEN');
        return $validToken && $token === $validToken;
    }

    private function getInHouseBirthdayGuests($days = 7, $includeToday = true)
    {
        $startDate = $includeToday ? Carbon::now() : Carbon::now()->addDay();
        $endDate = $startDate->copy()->addDays($days - 1);
        
        return Contact::whereHas('profilesfolio', function($query) {
                $query->where('foliostatus', 'I')
                      ->whereNotNull('room')
                      ->whereNotNull('dateci')
                      ->whereNotNull('dateco');
            })
            ->whereNotNull('birthday')
            ->where(function($query) use ($startDate, $endDate) {
                $startMonth = $startDate->month;
                $startDay = $startDate->day;
                $endMonth = $endDate->month;
                $endDay = $endDate->day;
                
                if ($endMonth == $startMonth) {
                    $query->whereRaw('MONTH(birthday) = ? AND DAY(birthday) BETWEEN ? AND ?', 
                        [$startMonth, $startDay, $endDay]);
                } else {
                    $query->where(function($q) use ($startMonth, $startDay, $endMonth, $endDay) {
                        $q->whereRaw('MONTH(birthday) = ? AND DAY(birthday) >= ?', [$startMonth, $startDay])
                          ->orWhereRaw('MONTH(birthday) = ? AND DAY(birthday) <= ?', [$endMonth, $endDay]);
                    });
                }
            })
            ->with(['profilesfolio' => function($query) {
                $query->where('foliostatus', 'I');
            }])
            ->get();
    }

    private function formatBirthdayData($guests)
    {
        $data = [];
        
        foreach ($guests as $guest) {
            $folio = $guest->profilesfolio->first();
            if (!$folio) continue;
            
            $birthday = Carbon::parse($guest->birthday);
            $dateci = Carbon::parse($folio->dateci);
            $dateco = Carbon::parse($folio->dateco);
            
            $data[] = [
                'crm_contact_id' => $guest->contactid,
                'fname' => $guest->fname,
                'lname' => $guest->lname,
                'folio_master' => $folio->folio_master,
                'folio' => $folio->folio,
                'foliostatus' => $folio->foliostatus,
                'room' => $folio->room,
                'roomtype' => $folio->roomtype,
                'dateci' => $dateci->format('Y-m-d'),
                'dateco' => $dateco->format('Y-m-d'),
                'birthday' => $birthday->format('Y-m-d'),
                'birthday_month_day' => $birthday->format('m-d'),
                'email' => $guest->email,
                'mobile' => $guest->mobile,
                'salutation' => $guest->salutation,
                'days_until_birthday' => $this->calculateDaysUntilBirthday($birthday)
            ];
        }
        
        return $data;
    }

    private function calculateDaysUntilBirthday($birthday)
    {
        $today = Carbon::now()->startOfDay(); // Set ke 00:00:00
        $thisYearBirthday = Carbon::create($today->year, $birthday->month, $birthday->day)->startOfDay();
        
        // Jika birthday sudah lewat (tanggal, bukan waktu)
        if ($thisYearBirthday->lt($today)) {
            $thisYearBirthday->addYear();
        }
        
        return $today->diffInDays($thisYearBirthday);
    }
}