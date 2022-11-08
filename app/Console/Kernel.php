<?php

namespace App\Console;

use App\Console\Commands\EnvatoSendMessageCommand;
use App\Console\Commands\EnvatoSendMediaCommand;
use App\Console\Commands\EnvatoZipVerifyCommand;
use App\Console\Commands\TestBotCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        EnvatoSendMessageCommand::class,
        EnvatoSendMediaCommand::class,
        EnvatoZipVerifyCommand::class,
        TestBotCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('xiaomi:id 1')->hourly();
        $schedule->command('xiaomi:id 2')->hourly();
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
