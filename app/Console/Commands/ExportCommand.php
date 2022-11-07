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

        $update = [];
        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_start = strtotime($date_start);
        $unix_end = strtotime($date_end);

        $date = date_parse_from_format("j.n.Y H:iP", $date_start);

        $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => $channel_id, 'limit' => 100]);
        $chat = $MTProto->MadelineProto->channels->getFullChannel(['channel' => $channel_id]);

        $structure = 'D:/JSONf/{channel_name}/{YYYY}/{MM}/{DD}/{HH}';

        foreach ($messages['messages'] as $message) {
            if ($message['date'] >= $unix_start && $message['date'] <= $unix_end) {
                print_r($message['date']);
                print_r(PHP_EOL);
                array_push($update, $message);
            }
        }

        $path = 'D:/JSONf/';

        //Title
        if(!is_dir( $path . $chat['chats'][0]['title'])){
            mkdir($path . $chat['chats'][0]['title']);
        }
        $path .= $chat['chats'][0]['title'] . '/';

        //Year
        if(!is_dir($path . $date['year'])){
            mkdir($path . $date['year']);
        }
           $path .= $date['year'] . '/';

        file_put_contents($path  . 'result.json', json_encode($update));

        //month
        if(!is_dir($path . $date['month'])){
            mkdir($path . $date['month']);
        }
        $path .= $date['month'] . '/';

        
    }
}
