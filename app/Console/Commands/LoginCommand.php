<?php

namespace App\Console\Commands;

use App\Services\MadelineProto\MTProtoService;
use Illuminate\Console\Command;

class LoginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login to MadelineProto';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $MTProto = new MTProtoService();
    }
}
