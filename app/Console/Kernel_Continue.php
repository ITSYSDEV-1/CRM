<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Main emaillog command - daily at 8:30 AM
        $schedule->command('emaillog')->dailyAt('08:30');
        
        // Check for continuation runs every 10 minutes
        $schedule->call(function () {
            try {
                $pendingRuns = DB::table('email_batch_progress')
                    ->where('status', 'pending')
                    ->where('next_run_time', '<=', now())
                    ->where('next_run_time', '>', now()->subHours(6)) // Safety check
                    ->get();
                    
                foreach ($pendingRuns as $run) {
                    // Execute continuation run
                    Artisan::call('emaillog', ['--continue' => true]);
                    
                    // Log the execution
                    Log::info('EmailLog continuation executed', [
                        'run_date' => $run->run_date,
                        'current_batch' => $run->current_batch,
                        'total_batches' => $run->total_batches,
                        'executed_at' => now()->toDateTimeString()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error in EmailLog continuation scheduler', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })->everyThreeMinutes();
        
        // Other scheduled commands
        // $schedule->command('poststay')->dailyAt('15:00');
        // $schedule->command('prestay')->dailyAt('15:00');
        $schedule->command('updatestatus')->dailyAt('15:30');
        // $schedule->command('birthdaymail')->dailyAt('11:59');
        // $schedule->command('campaign')->everyMinute();
        // $schedule->command('missyou')->dailyAt('11:59');
        // $schedule->command('syncemailresponse')->cron('* */5 * * *');
        // $schedule->command('emaillog')->cron('* */5 * * *'); // OLD - replaced with daily
        $schedule->command('validateemail')->hourly();
        $schedule->command('sync:iface-history')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
