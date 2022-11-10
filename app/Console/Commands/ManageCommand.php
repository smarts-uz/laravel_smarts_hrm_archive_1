<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Services\MadelineProto\MTProtoService;
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
        $handle = new MTProtoService();
        $handle->sync();
    }
}
