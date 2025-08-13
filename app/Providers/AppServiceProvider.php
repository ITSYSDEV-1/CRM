<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Configuration;
use App\Http\Controllers\PepipostMail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Basic configuration
        $configuration = Configuration::find(1);
        view()->share('configuration', $configuration);
        Schema::defaultStringLength(191);
    
        // Get quota info from cache if available
        $quotaInfo = cache()->get('email_quota_info');
        
        // If no cache, use default values from environment
        if (!$quotaInfo) {
            $quotaInfo = [
                'today_quota' => ['used' => 0, 'remaining' => env('EMAIL_QUOTA_DAILY_LIMIT', 5000)],
                'quota_used' => 0,
                'quota_remaining' => env('EMAIL_QUOTA_TOTAL', 150000),
                'billing_cycle' => [
                    'start' => now()->format('Y-m-d'),
                    'end' => now()->addMonth()->format('Y-m-d')
                ]
            ];
        }
        
        // Share with views for navbar
        view()->share('quotaInfo', $quotaInfo);
        
        // Share email configuration
        view()->share('emailConfig', [
            'sharing_account' => env('EMAIL_SHARING_ACCOUNT_NAME', 'RRP-RRPTG-PS'),
            'sharing_accounts' => explode(',', env('EMAIL_SHARING_ACCOUNTS', 'RRP-RRPTG-PS')),
            'daily_limit' => env('EMAIL_QUOTA_DAILY_LIMIT', 5000),
            'total_quota' => env('EMAIL_QUOTA_TOTAL', 150000)
        ]);
        
        // Only update cache if it's older than 30 minutes
        $shouldUpdate = !cache()->has('email_quota_last_update') || 
                       cache()->get('email_quota_last_update') < now()->subMinutes(30);
        
        if ($shouldUpdate && !app()->runningInConsole()) {
            // Mark as updated
            cache()->put('email_quota_last_update', now(), now()->addHours(1));
            
            // Update in background after response is sent
            app()->terminating(function () {
                try {
                    $mail = new PepipostMail();
                    $response = $mail->getEmailQuota();
                    
                    if (!isset($response['error']) && isset($response['quota_info'])) {
                        cache()->put('email_quota_info', $response['quota_info'], now()->addHours(1));
                    }
                } catch (\Exception $e) {
                    //\Log::warning('Background quota update failed: ' . $e->getMessage());
                }
            });
        }
    }
}
