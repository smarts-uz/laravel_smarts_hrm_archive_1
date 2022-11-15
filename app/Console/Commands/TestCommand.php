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
        $message = $MTProto->MadelineProto->messages->getHistory(['peer' => 1244414566, 'message' => ['messages']]);
        $update = [];
        foreach ($message['messages'] as $message) {
            $mess = [];
            $mess['id'] = $message['id'];
            $mess['type'] = $message['_'];
            $mess['date'] = date("j.n.Y H:iP", $message['date']);
            $mess['date_unixtime'] = (string)$message['date'];
            $mess['text'] = $message['message'];
            array_push($update, $mess);
        }
        print_r($update);

    }
}
