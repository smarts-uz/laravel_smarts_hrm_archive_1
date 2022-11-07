<?php

namespace App\Console\Commands;

use App\Services\Envato\PreviewVerifier\EnvatoSendMessageService;
use Illuminate\Console\Command;

class EnvatoSendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envato:sendMessage';

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
        $me = new EnvatoSendMessageService();
        $me->getPostId('2128', '2129');
    }
}
