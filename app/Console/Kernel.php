<?php

namespace App\Console;

use App\Models\Image;
use App\Models\Reminder;
use App\Utilities\Trash;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () { Trash::removeExpired(); })->daily();
        $schedule->call(function () { Reminder::sendExpired(); })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
