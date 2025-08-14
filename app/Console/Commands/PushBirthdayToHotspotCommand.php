<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\ProfileFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PushBirthdayToHotspotCommand extends Command
{
    protected $signature = 'birthday:push-to-hotspot {--test : Run in test mode} {--force : Force push even if recently sent}';
    protected $description = 'Push in-house guest birthday data to Hotspot Manager via webhook';

    private $maxRetries = 3;
    private $retryDelayMinutes = 15;

    public function handle()
    {
        $testMode = $this->option('test');
        $force = $this->option('force');
        
        if ($testMode) {
            $this->info('🧪 Running in TEST MODE - no data will be sent');
        }

        try {
            // Check if we should skip (unless forced)
            if (!$force && $this->shouldSkipPush()) {
                $this->info('⏭️ Skipping push - recently sent data');
                return 0;
            }

            // Get in-house guests with birthdays in next 7 days
            $birthdayGuests = $this->getInHouseBirthdayGuests();
            
            $this->info("🎂 Found {$birthdayGuests->count()} in-house guests with upcoming birthdays");
            
            if ($birthdayGuests->isEmpty()) {
                $this->info('📭 No birthday guests found. Exiting.');
                $this->logPushAttempt(0, 'success', 'No birthday guests found');
                return 0;
            }

            // Prepare data for Hotspot
            $birthdayData = $this->prepareBirthdayData($birthdayGuests);
            
            if ($testMode) {
                $this->displayTestData($birthdayData);
                return 0;
            }

            // Send to Hotspot Manager via webhook with retry mechanism
            $this->sendToHotspotWebhook($birthdayData);
            
            $this->info('✅ Birthday data successfully pushed to Hotspot Manager');
            
        } catch (\Exception $e) {
            $this->error('❌ Error pushing birthday data: ' . $e->getMessage());
            Log::error('Birthday push error', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'command' => 'birthday:push-to-hotspot'
            ]);
            return 1;
        }

        return 0;
    }

    private function shouldSkipPush()
    {
        // Check if we've sent data in the last 2.5 hours
        $lastPush = DB::table('birthday_push_log')
            ->where('status', 'success')
            ->where('created_at', '>', now()->subHours(2.5))
            ->exists();
            
        return $lastPush;
    }

    // private function shouldSkipPush()
    // {
    //     // Ubah dari 2.5 jam ke 30 detik untuk testing
    //     $lastPush = DB::table('birthday_push_log')
    //         ->where('status', 'success')
    //         ->where('created_at', '>', now()->subSeconds(30))
    //         ->exists();
            
    //     return $lastPush;
    // }

    private function getInHouseBirthdayGuests()
    {
        $today = Carbon::now();
        $nextWeek = $today->copy()->addDays(7);
        
        return Contact::whereHas('profilesfolio', function($query) {
                $query->where('foliostatus', 'I') // In-house status
                      ->whereNotNull('room')
                      ->whereNotNull('dateci')
                      ->whereNotNull('dateco');
            })
            ->whereNotNull('birthday')
            ->where(function($query) use ($today, $nextWeek) {
                // Handle birthday matching across year boundaries
                $todayMonth = $today->month;
                $todayDay = $today->day;
                $nextWeekMonth = $nextWeek->month;
                $nextWeekDay = $nextWeek->day;
                
                if ($nextWeekMonth == $todayMonth) {
                    // Same month
                    $query->whereRaw('MONTH(birthday) = ? AND DAY(birthday) BETWEEN ? AND ?', 
                        [$todayMonth, $todayDay, $nextWeekDay]);
                } else {
                    // Cross month boundary
                    $query->where(function($q) use ($todayMonth, $todayDay, $nextWeekMonth, $nextWeekDay) {
                        $q->whereRaw('MONTH(birthday) = ? AND DAY(birthday) >= ?', [$todayMonth, $todayDay])
                          ->orWhereRaw('MONTH(birthday) = ? AND DAY(birthday) <= ?', [$nextWeekMonth, $nextWeekDay]);
                    });
                }
            })
            ->with(['profilesfolio' => function($query) {
                $query->where('foliostatus', 'I');
            }])
            ->get();
    }

    private function prepareBirthdayData($guests)
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
                'sync_timestamp' => now()->toISOString()
            ];
        }
        
        return $data;
    }

    private function displayTestData($data)
    {
        $this->info('\n=== 🧪 TEST MODE - Data that would be sent ===');
        
        foreach ($data as $guest) {
            $this->line("👤 Guest: {$guest['fname']} {$guest['lname']}");
            $this->line("🏠 Room: {$guest['room']} ({$guest['roomtype']})");
            $this->line("🎂 Birthday: {$guest['birthday']}");
            $this->line("📋 Folio: {$guest['folio']} / Master: {$guest['folio_master']}");
            $this->line("📅 Stay: {$guest['dateci']} - {$guest['dateco']}");
            $this->line('---');
        }
        
        $this->info("\n📊 Total guests: " . count($data));
        $this->info("🌐 Would send to: " . env('HOTSPOT_WEBHOOK_URL'));
    }

    private function sendToHotspotWebhook($data)
    {
        $webhookUrl = env('HOTSPOT_WEBHOOK_URL');
        $webhookToken = env('HOTSPOT_WEBHOOK_TOKEN');
        
        if (!$webhookUrl) {
            throw new \Exception('HOTSPOT_WEBHOOK_URL not configured in environment');
        }

        $payload = [
            'source' => 'crm',
            'type' => 'birthday_sync',
            'timestamp' => now()->toISOString(),
            'data' => $data,
            'total_records' => count($data)
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'CRM-Birthday-Sync/1.0',
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json'
        ];
        
        if ($webhookToken) {
            $headers['Authorization'] = 'Bearer ' . $webhookToken;
        }

        $attempt = 1;
        $lastError = null;
        $syncTimestamp = now();

        while ($attempt <= $this->maxRetries) {
            try {
                $this->info("📡 Sending to Hotspot (attempt {$attempt}/{$this->maxRetries})...");
                
                $response = Http::timeout(30)
                    ->withHeaders($headers)
                    ->post($webhookUrl, $payload);

                if ($response->successful()) {
                    $this->info("✅ Successfully sent data (HTTP {$response->status()})");
                    $this->logPushAttempt(count($data), 'success', "HTTP {$response->status()}", $attempt, $data, $syncTimestamp);
                    return;
                }
                
                $lastError = "HTTP {$response->status()}: {$response->body()}";
                $this->warn("⚠️ Attempt {$attempt} failed: {$lastError}");
                
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $this->warn("⚠️ Attempt {$attempt} failed: {$lastError}");
            }

            if ($attempt < $this->maxRetries) {
                $this->info("⏳ Waiting {$this->retryDelayMinutes} minutes before retry...");
                sleep($this->retryDelayMinutes * 60);
            }
            
            $attempt++;
        }

        // All attempts failed
        $this->logPushAttempt(count($data), 'failed', $lastError, $this->maxRetries, $data, $syncTimestamp);
        throw new \Exception("Failed to send data after {$this->maxRetries} attempts. Last error: {$lastError}");
    }

    private function logPushAttempt($recordCount, $status, $message, $attempts = 1, $data = null, $syncTimestamp = null)
    {
        // Create guest summary for easy reading
        $guestSummary = null;
        if ($data && is_array($data)) {
            $summaryItems = [];
            foreach ($data as $guest) {
                $summaryItems[] = "{$guest['fname']} {$guest['lname']} (Room: {$guest['room']}, Birthday: {$guest['birthday']})";
            }
            $guestSummary = implode('; ', $summaryItems);
        }

        DB::table('birthday_push_log')->insert([
            'record_count' => $recordCount,
            'pushed_data' => $data ? json_encode($data) : null,
            'guest_summary' => $guestSummary,
            'sync_timestamp' => $syncTimestamp,
            'status' => $status,
            'message' => $message,
            'attempts' => $attempts,
            'webhook_url' => env('HOTSPOT_WEBHOOK_URL'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Simple log untuk monitoring
        if ($status === 'success') {
            Log::info("Birthday push successful: {$recordCount} guests sent to Hotspot", [
                'guest_count' => $recordCount,
                'webhook_url' => env('HOTSPOT_WEBHOOK_URL'),
                'sync_timestamp' => $syncTimestamp
            ]);
        } else {
            Log::error("Birthday push failed: {$message}", [
                'guest_count' => $recordCount,
                'attempts' => $attempts,
                'webhook_url' => env('HOTSPOT_WEBHOOK_URL')
            ]);
        }
    }
}