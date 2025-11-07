<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SendReminderNotification::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Kirim pengingat setiap hari jam 08:00
        $schedule->command('reminder:undangan')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
