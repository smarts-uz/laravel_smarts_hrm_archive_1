<?php

namespace App\Console\Commands;

use App\Services\Envato\PreviewVerifier\EnvatoSendMediaService;
use Illuminate\Console\Command;

class EnvatoSendMediaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envato:sendMedia';

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
        $me = new EnvatoSendMediaService();
        $me->getPostId('1', '1120');
    }
}
