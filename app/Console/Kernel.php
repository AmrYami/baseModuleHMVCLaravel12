<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule): void
    {
        // Security hygiene: prune expired Sanctum tokens and stale password reset tokens nightly.
        $schedule->command('sanctum:prune-expired --hours=24')->dailyAt('01:15')->withoutOverlapping();
        $schedule->command('auth:clear-resets')->dailyAt('01:30')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
