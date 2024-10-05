<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('notification:check-unavailable')->everyMinute();
        $schedule->command('app:reject-pending-requests')->everyMinute();
        $schedule->command('app:event-reminder-command')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
