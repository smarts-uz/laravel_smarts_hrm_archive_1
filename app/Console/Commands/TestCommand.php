<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\MTProtoService;
use App\Services\NutgramService;
use App\Services\PythonService;
use Exception;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

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
        $MTProto = new \App\Services\MTProtoService();

        $offset = '';
        $end = '';

        if($end == null){

        }else{
            $diff = explode('/',$offset)[5] - explode('/',$end)[5];
            print_r($diff);


        }



        $comments = $MTProto->getComments('https://t.me/c/1807426588/530');
        print_r($comments);

    }
}
