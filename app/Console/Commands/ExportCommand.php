<?php

namespace App\Console\Commands;

use App\Services\MTProtoService;
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

        $update = [];
        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_start = strtotime($date_start);
        $unix_end = strtotime($date_end);

        $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => $channel_id, 'limit' => 100]);
        $chat = $MTProto->MadelineProto->channels->getFullChannel(['channel' => $channel_id]);

        $structure = 'D:/JSONs/{channel_name}/{YYYY}/{MM}/{DD}/{HH}';

        foreach ($messages['messages'] as $message) {
            if ($message['date'] >= $unix_start && $message['date'] <= $unix_end) {
                print_r($message['date']);
                print_r(PHP_EOL);
                array_push($update, $message);
            }
        }

        $path = '';

        //Title
        if(!is_dir('D:/JSONs/' . $chat['chats'][0]['title'])){
            mkdir('D:/JSONs/' . $chat['chats'][0]['title']);
            $path = 'D:/JSONs/' . $chat['chats'][0]['title'];
        }
        //Year
        if(!is_dir($path . $chat['chats'][0]['title'])){
            mkdir($path . $chat['chats'][0]['title']);
            $path = $path . $chat['chats'][0]['title'];
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
