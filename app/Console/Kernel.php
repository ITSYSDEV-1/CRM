<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // Processed & Delivered - 3 times daily (every 8 hours)
        // Jam 00:00, 08:00, 16:00
        $schedule->command('emaillog --fetch-type=processed-delivered')->cron('0 0,8,16 * * *');
        
        // Opened - 2 times daily (every 12 hours) 
        // Jam 06:00, 18:00
        $schedule->command('emaillog --fetch-type=opened')->cron('0 6,18 * * *');
        
        // Other scheduled commands
        // $schedule->command('poststay')->dailyAt('15:00');
        // $schedule->command('prestay')->dailyAt('15:00');
        $schedule->command('updatestatus')->dailyAt('15:30');
        // $schedule->command('birthdaymail')->dailyAt('11:59');
        // $schedule->command('campaign')->everyMinute();
        // $schedule->command('missyou')->dailyAt('11:59');
        // $schedule->command('syncemailresponse')->hourly();
        $schedule->command('validateemail')->cron('0 */6 * * *');
        $schedule->command('sync:iface-history')->cron('0 */6 * * *');
        
        // Push birthday data to Hotspot every 3 hours 30 minutes
        // Push birthday data to Hotspot - hanya jika diaktifkan di environment
        if (env('BIRTHDAY_PUSH_ENABLED', false)) {
            $schedule->command('birthday:push-to-hotspot')
                     ->cron('30 */3 * * *') // 00:30, 03:30, 06:30, dst.
                     ->withoutOverlapping()
                     ->runInBackground();

            // // Temporary: ubah ke setiap menit untuk testing (hanya jika diaktifkan)
            // if (env('BIRTHDAY_PUSH_ENABLED', false) && env('APP_ENV') === 'local') {
            //     $schedule->command('birthday:push-to-hotspot')
            //              ->everyMinute() // Ganti sementara untuk testing
            //              ->withoutOverlapping()
            //              ->runInBackground();
            // }
        }
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
