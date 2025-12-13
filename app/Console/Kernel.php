<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendOverdueWorkflowReminders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Run overdue reminders every 15 minutes
        $schedule->command('workflow:reminders:overdue')->everyFifteenMinutes();
        // Nightly rebuild of answer usage aggregates
        if (config('assessments.usage_aggregates_cron')) {
            // Nightly incremental (last 24h)
            $schedule->command('assessments:rebuild-answer-usage', [
                '--since' => now()->subDay()->toDateString(),
                '--chunk' => 1000,
            ])->dailyAt('02:00')->withoutOverlapping();
            // Weekly deep rebuild (full)
            $schedule->command('assessments:rebuild-answer-usage', [
                '--full' => true,
                '--chunk' => 5000,
            ])->sundays()->at('03:00')->withoutOverlapping();
        }
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
