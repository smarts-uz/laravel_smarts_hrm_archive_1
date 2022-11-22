<?php

namespace App\Console\Commands;

use App\Services\DinnerBot\DinnerBotService;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class DinnerBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinner:run';

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
        $manage = new DinnerBotService();
        $bot = new Nutgram(env('DROPPER_BOT_TOKEN'));
        $manage->handle($bot);
        $bot->run();
    }
}
