<?php

namespace App\Console\Commands;

use App\Services\EnvatoService;
use Illuminate\Console\Command;
use danog\MadelineProto\API;
class EnvatoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envato:run';

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
        $me = new EnvatoService();
        $me->Previews('https://t.me/c/1884117700/700', 'https://t.me/c/1884117700/773');
        //1897777451
        //639086927
        //1857450597
        //1884117700
    }
}
