<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MadelineProto\Database\GetChatService;

class GetChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:run';

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
    public function handle(GetChatService $chatService)
    {
        $chatService->fill();
    }
}
