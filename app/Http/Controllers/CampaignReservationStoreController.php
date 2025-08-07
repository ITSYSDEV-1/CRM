<?php

namespace App\Http\Controllers;

use App\Helpers\SegmentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Segment;
use App\Models\ExternalContactCategory;
use App\Models\ExcludedEmail;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Traits\UserLogsActivity;

class CampaignReservationStoreController extends Controller
{
    use UserLogsActivity;

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Tentukan tipe campaign
            if ($request->category == '' || $request->category == null) {
                $type = 'internal';
                // Perbaikan: ambil elemen pertama dari array segments
                $segment = is_array($request->segments) ? $request->segments[0] : $request->segments;
            } else {
                $type = 'external';
                $segment = $request->category;
            }
    
            // Debug logging
            Log::info('Campaign Debug - Start', [
                'type' => $type,
                'segment_id' => $segment,
                'request_data' => $request->all()
            ]);
    
            // Buat campaign utama
            $campaign = new Campaign();
            $campaign->name = $request->name;
            $campaign->status = 'scheduled';
            $campaign->type = $type;
            $campaign->template_id = $request->template;
            $campaign->save();
    
            // Ambil contacts berdasarkan segment dengan detail info
            $contactsInfo = $this->getContactsBySegmentWithDetails($type, $segment);
            $allContacts = $contactsInfo['valid_contacts'];
            $totalRecipients = count($allContacts);
            $totalFoundBeforeExclusion = $contactsInfo['total_before_exclusion'];
            $totalExcluded = $contactsInfo['total_excluded'];
    
            // Debug logging
            Log::info('Campaign Debug - Recipients Found', [
                'total_recipients' => $totalRecipients,
                'total_before_exclusion' => $totalFoundBeforeExclusion,
                'total_excluded' => $totalExcluded,
                'campaign_id' => $campaign->id
            ]);
    
            // Enhanced error handling
            if ($totalRecipients == 0) {
                if ($totalFoundBeforeExclusion == 0) {
                    throw new \Exception('No contacts found matching the segment criteria');
                } else {
                    throw new \Exception('All recipients (' . $totalFoundBeforeExclusion . ' contacts) are under excluded emails list');
                }
            }
    
            if ($totalRecipients == 0) {
                throw new \Exception('No valid recipients found for this campaign');
            }
    
            // Request approval ke campaign center
            $approvalResponse = $this->requestCampaignApproval([
                'campaign_id' => $campaign->id,
                'scheduled_date' => $request->schedule,
                'email_count' => $totalRecipients,
                'campaign_type' => $type === 'internal' ? 'internal' : 'promotional',
                'subject' => $campaign->name,
                'description' => 'Campaign: ' . $campaign->name
            ]);
    
            // Process response berdasarkan tipe
            $result = $this->processApprovalResponse($campaign, $allContacts, $approvalResponse, $request);
    
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'campaigns_created' => $result['campaigns_created'],
                'campaign_ids' => $result['campaign_ids']
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            
            // Debug logging untuk error
            Log::error('Campaign Debug - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getContactsBySegmentWithDetails($type, $segment)
    {
        $ex = [];
        $excluded = ExcludedEmail::all();
        foreach ($excluded as $exc) {
            array_push($ex, $exc->email);
        }
    
        Log::info('Campaign Debug - Excluded Emails', [
            'excluded_count' => count($ex)
        ]);
    
        $validContacts = [];
        $totalFoundBeforeExclusion = 0;
        $totalExcluded = 0;
    
        if ($type == 'internal') {
            $seg = Segment::find($segment);
            
            if (!$seg) {
                Log::error('Campaign Debug - Segment Not Found', ['segment_id' => $segment]);
                return [
                    'valid_contacts' => [],
                    'total_before_exclusion' => 0,
                    'total_excluded' => 0
                ];
            }
    
            // Debug segment data
            Log::info('Campaign Debug - Segment Data', [
                'segment_id' => $seg->id,
                'segment_name' => $seg->name,
                'country_id_raw' => $seg->country_id,
                'area_raw' => $seg->area,
                'guest_status_raw' => $seg->guest_status,
                'gender_raw' => $seg->gender,
                'booking_source_raw' => $seg->booking_source
            ]);
    
            // Debug unserialized data
            $countryIds = SegmentHelper::safeUnserialize($seg->country_id);
            $areas = SegmentHelper::safeUnserialize($seg->area);
            $guestStatuses = SegmentHelper::safeUnserialize($seg->guest_status);
            $genders = SegmentHelper::safeUnserialize($seg->gender);
            $bookingSources = SegmentHelper::safeUnserialize($seg->booking_source);
    
            Log::info('Campaign Debug - Unserialized Data', [
                'country_ids' => $countryIds,
                'areas' => $areas,
                'guest_statuses' => $guestStatuses,
                'genders' => $genders,
                'booking_sources' => $bookingSources
            ]);
    
            // Start dengan semua contacts
            $contactsQuery = Contact::with('transaction', 'profilesfolio');
            
            // Debug total contacts sebelum filter
            $totalContactsBeforeFilter = Contact::count();
            Log::info('Campaign Debug - Total Contacts Before Filter', [
                'total' => $totalContactsBeforeFilter
            ]);
    
            // Apply filters step by step dengan logging
            if (SegmentHelper::hasValidData($seg->country_id)) {
                $contactsQuery->whereIn('country_id', $countryIds);
                $countAfterCountryFilter = $contactsQuery->count();
                Log::info('Campaign Debug - After Country Filter', [
                    'count' => $countAfterCountryFilter,
                    'filter_values' => $countryIds
                ]);
            }
    
            if (SegmentHelper::hasValidData($seg->area)) {
                $contactsQuery->whereIn('area', $areas);
                $countAfterAreaFilter = $contactsQuery->count();
                Log::info('Campaign Debug - After Area Filter', [
                    'count' => $countAfterAreaFilter,
                    'filter_values' => $areas
                ]);
            }
    
            if (SegmentHelper::hasValidData($seg->guest_status)) {
                $contactsQuery->whereHas('profilesfolio', function ($q) use ($guestStatuses) {
                    return $q->whereIn('foliostatus', $guestStatuses);
                });
                $countAfterGuestStatusFilter = $contactsQuery->count();
                Log::info('Campaign Debug - After Guest Status Filter', [
                    'count' => $countAfterGuestStatusFilter,
                    'filter_values' => $guestStatuses
                ]);
            }
    
            if (SegmentHelper::hasValidData($seg->gender)) {
                $contactsQuery->whereIn('gender', $genders);
                $countAfterGenderFilter = $contactsQuery->count();
                Log::info('Campaign Debug - After Gender Filter', [
                    'count' => $countAfterGenderFilter,
                    'filter_values' => $genders
                ]);
            }
    
            if (SegmentHelper::hasValidData($seg->booking_source)) {
                $contactsQuery->whereHas('profilesfolio', function ($q) use ($bookingSources) {
                    return $q->whereIn('source', $bookingSources);
                });
                $countAfterBookingSourceFilter = $contactsQuery->count();
                Log::info('Campaign Debug - After Booking Source Filter', [
                    'count' => $countAfterBookingSourceFilter,
                    'filter_values' => $bookingSources
                ]);
            }
    
            // Add other filters (spending, age, dates, etc.)
            if ($seg->spending_from && $seg->spending_to) {
                $contactsQuery->whereHas('transaction', function ($q) use ($seg) {
                    return $q->whereBetween('revenue', [
                        str_replace('.', '', $seg->spending_from),
                        str_replace('.', '', $seg->spending_to)
                    ]);
                });
            } elseif ($seg->spending_from) {
                $contactsQuery->whereHas('transaction', function ($q) use ($seg) {
                    return $q->where('revenue', '>=', str_replace('.', '', $seg->spending_from));
                });
            } elseif ($seg->spending_to) {
                $contactsQuery->whereHas('transaction', function ($q) use ($seg) {
                    return $q->where('revenue', '<=', str_replace('.', '', $seg->spending_to));
                });
            }
    
            if ($seg->age_from && $seg->age_to) {
                $contactsQuery->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) BETWEEN ? AND ?', 
                    [$seg->age_from, $seg->age_to]);
            }
    
            // Get final results
            $contacts = $contactsQuery->get();
            $totalFoundBeforeExclusion = $contacts->count();
    
            Log::info('Campaign Debug - Final Query Results', [
                'total_found' => $totalFoundBeforeExclusion
            ]);
    
            // Filter out excluded emails with counting
            foreach ($contacts as $contact) {
                if (in_array($contact->email, $ex) || empty($contact->email)) {
                    $totalExcluded++;
                    Log::debug('Campaign Debug - Contact Excluded', [
                        'email' => $contact->email,
                        'contact_id' => $contact->contactid,
                        'reason' => empty($contact->email) ? 'Empty email' : 'In excluded list'
                    ]);
                } else {
                    $validContacts[] = $contact;
                }
            }
    
            Log::info('Campaign Debug - After Email Exclusion', [
                'valid_contacts' => count($validContacts),
                'total_excluded' => $totalExcluded
            ]);
    
        } else {
            $cat = ExternalContactCategory::find($segment);
            if ($cat && $cat->email) {
                $contacts = $cat->email;
                $totalFoundBeforeExclusion = count($contacts);
                
                foreach ($contacts as $contact) {
                    if (in_array($contact->email, $ex) || empty($contact->email)) {
                        $totalExcluded++;
                    } else {
                        $validContacts[] = $contact;
                    }
                }
            }
        }
    
        return [
            'valid_contacts' => $validContacts,
            'total_before_exclusion' => $totalFoundBeforeExclusion,
            'total_excluded' => $totalExcluded
        ];
    }

    private function requestCampaignApproval($data)
    {
        try {
            $campaignCenterUrl = env('CAMPAIGN_CENTER_URL') . '/api/schedule/request';
            $apiToken = env('CAMPAIGN_CENTER_API_TOKEN');
    
            // Convert date format from "06 Aug 2025  03:00 pm" to "2025-08-06"
            $scheduledDate = $data['scheduled_date'];
            try {
                // Parse the date and convert to Y-m-d format
                $dateObj = \Carbon\Carbon::createFromFormat('d M Y  h:i a', $scheduledDate);
                $formattedDate = $dateObj->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback: try to parse with different format or use current date
                Log::warning('Date parsing failed, using current date', [
                    'original_date' => $scheduledDate,
                    'error' => $e->getMessage()
                ]);
                $formattedDate = now()->format('Y-m-d');
            }
    
            // Mapping campaign type untuk Campaign Center
            $campaignTypeMapping = [
            'internal' => 'promotional',
            'external' => 'promotional',
            // tambahkan mapping lain sesuai kebutuhan
            ];
            
            $mappedCampaignType = $campaignTypeMapping[$data['campaign_type']] ?? 'promotional';
    
            $requestData = [
            'app_code' => env('CAMPAIGN_CENTER_CODE'),
            'scheduled_date' => $formattedDate, 
            'email_count' => $data['email_count'],
            'campaign_type' => $mappedCampaignType,
            'subject' => $data['subject'],
            'description' => $data['description']
            ];
    
            // Log request details
            Log::info('Campaign Center Request', [
                'url' => $campaignCenterUrl,
                'original_date' => $scheduledDate,
                'formatted_date' => $formattedDate,
                'request_data' => $requestData,
                'has_token' => !empty($apiToken)
            ]);
    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json'
            ])->timeout(60)->post($campaignCenterUrl, $requestData);
    
            // Log response details
            Log::info('Campaign Center Response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'response_body' => $response->body()
            ]);
    
            if ($response->successful()) {
                Log::info('Campaign Center Request SUCCESS');
                return $response->json();
            } else {
                Log::error('Campaign Center Request FAILED', [
                    'status_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                // Throw exception instead of fallback
                throw new \Exception('Campaign Center tidak merespons dengan baik. Status: ' . $response->status() . '. Silakan coba beberapa saat lagi.');
                
                // FALLBACK MECHANISM - Uncomment lines below to enable fallback approval
                // return $this->getFallbackApproval($data);
            }
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle connection timeout specifically
            Log::error('Campaign Center Connection Timeout', [
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Tidak dapat terhubung ke Campaign Center. Silakan periksa koneksi internet dan coba beberapa saat lagi.');
            
        } catch (\Exception $e) {
            // Log the actual error
            Log::error('Campaign Center Request Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's a timeout error
            if (strpos($e->getMessage(), 'timeout') !== false || strpos($e->getMessage(), 'timed out') !== false) {
                throw new \Exception('Campaign Center tidak merespons dalam waktu yang ditentukan. Silakan coba beberapa saat lagi.');
            }
            
            // Re-throw the original exception with user-friendly message
            throw new \Exception('Terjadi kesalahan saat menghubungi Campaign Center: ' . $e->getMessage() . '. Silakan coba beberapa saat lagi.');
            
            // FALLBACK MECHANISM - Uncomment lines below to enable fallback approval
            // return $this->getFallbackApproval($data);
        }
    }

    // FALLBACK MECHANISM - Uncomment method below to enable fallback approval
    /*
    private function getFallbackApproval($data)
    {
        Log::warning('Campaign Center Fallback Approval', [
            'campaign_id' => $data['campaign_id'],
            'email_count' => $data['email_count'],
            'scheduled_date' => $data['scheduled_date']
        ]);
        
        return [
            'success' => true,
            'type' => 'full_approval',
            'message' => 'Campaign partially approved with auto-booking (fallback)',
            'data' => [
                'campaign_id' => 'fallback_' . $data['campaign_id'],
                'scheduled_date' => $data['scheduled_date'],
                'approved_count' => $data['email_count']
            ]
        ];
    }
    */

    private function processApprovalResponse($originalCampaign, $allContacts, $approvalResponse, $request)
    {
        if (!$approvalResponse['success']) {
            $originalCampaign->status = 'Rejected';
            $originalCampaign->save();
            throw new \Exception('Campaign rejected by campaign center');
        }

        $campaignsCreated = [];
        $campaignIds = [];

        switch ($approvalResponse['type']) {
            case 'full_approval':
                $result = $this->handleFullApproval($originalCampaign, $allContacts, $approvalResponse['data'], $request);
                $campaignsCreated[] = $result;
                $campaignIds[] = $result->id;
                break;

            case 'full_auto_booking':
                $result = $this->handleFullAutoBooking($originalCampaign, $allContacts, $approvalResponse['data'], $request);
                $campaignsCreated = $result;
                $campaignIds = array_map(function($c) { return $c->id; }, $result);
                break;

            case 'partial_approval_with_auto_booking':
                $result = $this->handlePartialApprovalWithAutoBooking($originalCampaign, $allContacts, $approvalResponse['data'], $request);
                $campaignsCreated = $result;
                $campaignIds = array_map(function($c) { return $c->id; }, $result);
                break;

            default:
                throw new \Exception('Unknown approval response type: ' . $approvalResponse['type']);
        }

        return [
            'message' => $approvalResponse['message'],
            'campaigns_created' => count($campaignsCreated),
            'campaign_ids' => $campaignIds
        ];
    }

    private function handleFullApproval($campaign, $allContacts, $data, $request)
    {
        // Attach semua contacts ke campaign asli
        $this->attachContactsToCompaign($campaign, $allContacts, $request);
        
        // Set schedule, status, dan campaign_center_id
        $campaign->status = 'Scheduled';
        $campaign->campaign_center_id = $data['campaign_id']; // Simpan campaign center ID
        $campaign->save();
        $this->setSheduleFunc($campaign->id, $data['scheduled_date']);
        
        return $campaign;
    }

    private function handleFullAutoBooking($originalCampaign, $allContacts, $data, $request)
    {
        $campaignsCreated = [];
        $contactIndex = 0;
        
        // Simpan nama asli sebelum dimodifikasi
        $originalName = $originalCampaign->name;
        
        // Handle main campaign - ubah nama menjadi Auto Booked 1
        if (isset($data['main_campaign'])) {
            $mainData = $data['main_campaign'];
            $mainContacts = array_slice($allContacts, $contactIndex, $mainData['email_count']);
            $contactIndex += $mainData['email_count'];
            
            // Ubah nama campaign parent menjadi Auto Booked 1
            $originalCampaign->name = $originalName . ' - Auto Booked 1';
            $originalCampaign->campaign_center_id = $mainData['campaign_id']; // Simpan campaign center ID
            $this->attachContactsToCompaign($originalCampaign, $mainContacts, $request);
            $originalCampaign->status = 'Scheduled';
            $originalCampaign->save();
            $this->setSheduleFunc($originalCampaign->id, $mainData['scheduled_date']);
            
            $campaignsCreated[] = $originalCampaign;
            $startingIndex = 2;
        } else {
            // Jika tidak ada main_campaign, gunakan originalCampaign sebagai Auto Booked 1
            if (isset($data['auto_booked_campaigns']) && count($data['auto_booked_campaigns']) > 0) {
                $firstAutoBooking = $data['auto_booked_campaigns'][0];
                
                $originalCampaign->name = $originalName . ' - Auto Booked 1';
                $originalCampaign->status = 'Scheduled';
                $originalCampaign->campaign_center_id = $firstAutoBooking['campaign_id']; // Simpan campaign center ID
                
                $recipientsCount = $firstAutoBooking['email_count'];
                $campaignContacts = array_slice($allContacts, $contactIndex, $recipientsCount);
                $contactIndex += $recipientsCount;
                
                $this->attachContactsToCompaign($originalCampaign, $campaignContacts, $request);
                $this->setSheduleFunc($originalCampaign->id, $firstAutoBooking['date']);
                $originalCampaign->save();
                
                $campaignsCreated[] = $originalCampaign;
            }
            $startingIndex = 2;
        }
        
        // Handle auto booked campaigns
        if (isset($data['auto_booked_campaigns'])) {
            // Skip first auto_booked_campaign jika tidak ada main_campaign (sudah diproses di atas)
            $autoBookedToProcess = isset($data['main_campaign']) ? 
                $data['auto_booked_campaigns'] : 
                array_slice($data['auto_booked_campaigns'], 1);
            
            foreach ($autoBookedToProcess as $index => $autoBooking) {
                $newCampaign = new Campaign();
                $newCampaign->name = $originalName . ' - Auto Booked ' . ($index + $startingIndex);
                $newCampaign->status = 'Scheduled';
                $newCampaign->type = $originalCampaign->type;
                $newCampaign->template_id = $originalCampaign->template_id;
                $newCampaign->parent_campaign_id = $originalCampaign->id;
                $newCampaign->campaign_center_id = $autoBooking['campaign_id']; // Simpan campaign center ID
                $newCampaign->save();
                
                $recipientsCount = $autoBooking['email_count'];
                $campaignContacts = array_slice($allContacts, $contactIndex, $recipientsCount);
                $contactIndex += $recipientsCount;
                
                $this->attachContactsToCompaign($newCampaign, $campaignContacts, $request);
                $this->setSheduleFunc($newCampaign->id, $autoBooking['date']);
                
                $campaignsCreated[] = $newCampaign;
            }
        }
        
        return $campaignsCreated;
    }

    private function handlePartialApprovalWithAutoBooking($originalCampaign, $allContacts, $data, $request)
    {
        $campaignsCreated = [];
        $contactIndex = 0;
        
        // Simpan nama asli sebelum dimodifikasi
        $originalName = $originalCampaign->name;
        
        // Handle main campaign - ubah nama menjadi Auto Booked 1
        if (isset($data['main_campaign'])) {
            $mainData = $data['main_campaign'];
            $mainContacts = array_slice($allContacts, $contactIndex, $mainData['email_count']);
            $contactIndex += $mainData['email_count'];
            
            // Ubah nama campaign parent menjadi Auto Booked 1
            $originalCampaign->name = $originalName . ' - Auto Booked 1';
            $originalCampaign->campaign_center_id = $mainData['campaign_id']; // Simpan campaign center ID
            $this->attachContactsToCompaign($originalCampaign, $mainContacts, $request);
            $originalCampaign->status = 'Scheduled';
            $originalCampaign->save();
            $this->setSheduleFunc($originalCampaign->id, $mainData['scheduled_date']);
            
            $campaignsCreated[] = $originalCampaign;
        }
        
        // Handle auto booked campaigns - mulai dari index 2
        if (isset($data['auto_booked_campaigns'])) {
            foreach ($data['auto_booked_campaigns'] as $index => $autoBooking) {
                $newCampaign = new Campaign();
                // Gunakan nama asli, bukan nama yang sudah dimodifikasi
                $newCampaign->name = $originalName . ' - Auto Booked ' . ($index + 2);
                $newCampaign->status = 'Scheduled';
                $newCampaign->type = $originalCampaign->type;
                $newCampaign->template_id = $originalCampaign->template_id;
                $newCampaign->parent_campaign_id = $originalCampaign->id;
                $newCampaign->campaign_center_id = $autoBooking['campaign_id']; // Simpan campaign center ID
                $newCampaign->save();
                
                $recipientsCount = $autoBooking['email_count'];
                $campaignContacts = array_slice($allContacts, $contactIndex, $recipientsCount);
                $contactIndex += $recipientsCount;
                
                $this->attachContactsToCompaign($newCampaign, $campaignContacts, $request);
                $this->setSheduleFunc($newCampaign->id, $autoBooking['date']);
                
                $campaignsCreated[] = $newCampaign;
            }
        }
        
        return $campaignsCreated;
    }

    private function attachContactsToCompaign($campaign, $contacts, $request)
    {
        if ($campaign->type === 'internal') {
            foreach ($contacts as $contact) {
                $campaign->contact()->attach($contact, ['status' => 'queue']);
            }
            $campaign->segment()->attach($request->segments);
        } else {
            foreach ($contacts as $contact) {
                $campaign->external()->attach($contact, ['status' => 'queue']);
            }
            $campaign->externalSegment()->attach($request->category);
        }
        
        $campaign->template()->attach($request->template);
    }

    private function setSheduleFunc($campaignId, $scheduleDate)
    {
        try {
            $formattedDateTime = null;
            
            // Coba format dari campaign center (Y-m-d)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $scheduleDate)) {
                $dateObj = \Carbon\Carbon::createFromFormat('Y-m-d', $scheduleDate);
                $formattedDateTime = $dateObj->format('Y-m-d H:i:s');
            } 
            // Coba format dari request awal (d M Y h:i a)
            else {
                $dateObj = \Carbon\Carbon::createFromFormat('d M Y  h:i a', $scheduleDate);
                $formattedDateTime = $dateObj->format('Y-m-d H:i:s');
            }
            
            Log::info('Schedule Parsing Success', [
                'original' => $scheduleDate,
                'parsed' => $formattedDateTime
            ]);
        } catch (\Exception $e) {
            // Fallback: gunakan tanggal dari input dengan waktu default
            Log::warning('Schedule Parsing Failed', [
                'original' => $scheduleDate,
                'error' => $e->getMessage()
            ]);
            
            // Coba parsing dengan Carbon::parse sebagai fallback terakhir
            try {
                $dateObj = \Carbon\Carbon::parse($scheduleDate);
                $formattedDateTime = $dateObj->format('Y-m-d H:i:s');
            } catch (\Exception $e2) {
                $formattedDateTime = now()->format('Y-m-d H:i:s');
            }
        }
        
        $schedule = new Schedule();
        $schedule->campaign_id = $campaignId;
        $schedule->schedule = $formattedDateTime;
        $schedule->save();
        
        Log::info('Schedule Saved', [
            'campaign_id' => $campaignId,
            'schedule' => $formattedDateTime
        ]);
    }
}
