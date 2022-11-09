<?php

namespace App\Console\Commands;

use App\Services\Envato\ZipVerifier\VerifierService;
use Illuminate\Console\Command;

class ZipVerifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zipper';

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
        $verifier = new VerifierService();

        $start = readline('Enter start position: ');
        $end = readline('Enter end position: ');


        $verifier->verifier($start, $end);
    }
}
