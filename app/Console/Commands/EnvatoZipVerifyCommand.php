<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Envato\ZipVerifier\VerifierService;

class EnvatoZipVerifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'envato:zip-verifie';

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
        $verify = new VerifierService();
        $verify->verifier(100, 200);
    }
}
