<?php

namespace App\Console\Commands;

use App\Services\MadelineProto\MTProtoService;
use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export';

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
        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        $unix_start = strtotime($date_start);
        $unix_end = strtotime($date_end);
        $structure = 'D:/JSONs/{channel_name}/{YYYY}/{MM}/{DD}/{HH}';
        $path = '';

        //Title
        if(!is_dir('D:/JSONs/' . $chat['chats'][0]['title'])){
            mkdir('D:/JSONs/' . $chat['chats'][0]['title']);
            $path = 'D:/JSONs/' . $chat['chats'][0]['title'] . '/';
        }
        //Year
        if(!is_dir($path . $date['year'])){
            mkdir($path . $date['year']);
            $path = $path . $date['year'] . '/';
        }


        //Month


        //Day


        //Hour
        if(!is_dir('D:/JSONs/' . $chat['chats'][0]['title'] . '/' . $date_start)){
            mkdir('D:/JSONs/' . $chat['chats'][0]['title'] . '/' . $date_start);
        }
        file_put_contents('D:/JSONs/' . $chat['chats'][0]['title'] . '/' . $date_start  . '/result.json', json_encode($update));
    }
}
