<?php

namespace App\Console\Commands\__;

use App\Services\ManageService;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;


class NutgramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:nutgram';

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
        $manage = new ManageService();
        $bot = new Nutgram(env('DROPPER_BOT_TOKEN'));
        $manage->handle($bot);
        $bot->run();

    }
}
