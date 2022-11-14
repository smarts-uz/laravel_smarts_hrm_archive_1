<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Services\MadelineProto\MTProtoService;
use App\Services\TaskStatus\HandleStatusService;
use Illuminate\Console\Command;

class ManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:user';

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
        $Mtproto = new MTProtoService();
        HandleStatusService::startAndLoop(env('SESSION_PUT') . '/session.madeline', $Mtproto->settings);
    }
}
