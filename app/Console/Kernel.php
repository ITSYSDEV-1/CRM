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

        $schedule->command('poststay')->dailyAt('15:00');
        $schedule->command('prestay')->dailyAt('15:00');
        $schedule->command('updatestatus')->dailyAt('15:30');
        $schedule->command('birthdaymail')->dailyAt('11:59');
        $schedule->command('campaign')->everyMinute();
        $schedule->command('missyou')->dailyAt('11:59');
        $schedule->command('syncemailresponse')->cron('* */5 * * *');
        $schedule->command('emaillog')->cron('* */5 * * *');
        $schedule->command('validateemail')->hourly();
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
