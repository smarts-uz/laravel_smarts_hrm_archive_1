<?php

namespace App\Console\Commands;

use App\Services\RemoveJoinLeave\RemoveJoinLeaveService;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class RemoveJoinLeaveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:join';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handles and remove all join/leave messages';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $manage = new RemoveJoinLeaveService();
        $bot = new Nutgram(env('REMOVE_JOIN_BOT_TOKEN'));
        $manage->handle($bot);
        $bot->run();
    }
}
