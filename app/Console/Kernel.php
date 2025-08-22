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
        // Flip devices offline when SLA exceeded
        $schedule->job(new \App\Jobs\OfflineSweeper)->everyMinute();

        // Generate rollups every 5 minutes (or run as a nightly backfill job)
        $schedule->command('telemetry:rollup-5m')->everyFiveMinutes();

        // Retention policies
        $schedule->command('telemetry:prune-raw')->dailyAt('02:10');
        $schedule->command('logs:prune-archive')->dailyAt('02:40');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
