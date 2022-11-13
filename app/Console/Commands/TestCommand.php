<?php

namespace App\Console\Commands;

use App\Services\Envato\EnvatoService;
use App\Services\Envato\ZipVerifier\VerifierService;
use App\Services\MadelineProto\MTProtoService;
use Exception;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

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

        $MTProto = new MTProtoService();

        /*$start = readline('Enter start position: ');
        $end = readline('Enter end position: ');


        $verifyZip = new VerifierService();

        $verifyZip->verifier($start, $end);*/

    }
}
