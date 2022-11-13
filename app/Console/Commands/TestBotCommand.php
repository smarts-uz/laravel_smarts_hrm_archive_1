<?php

namespace App\Console\Commands;

use App\Services\TestBot;
use danog\MadelineProto\Settings;
use Illuminate\Console\Command;

class TestBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testBot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bot = new TestBot();
        $settings = new Settings;
        $bot::startAndLoop(env('SESSION_PUT'), $settings);
    }
}
