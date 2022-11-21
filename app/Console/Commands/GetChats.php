<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MadelineProto\Database\ChatService;

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
    public function handle(ChatService $chatService)
    {
        $a = $chatService->fill();
    }
}
