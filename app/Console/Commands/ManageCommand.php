<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Services\MadelineProto\MTProtoService;
use App\Services\TaskStatus\HandleStatusService;
use danog\MadelineProto\messages;
use danog\MadelineProto\MTProto;
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
        $Mtproto = new MTProtoService();
//        do {
            $history = $Mtproto->MadelineProto->messages->getHistory(["peer" => -1001851760117]);
        /*file_put_contents('history.json', json_encode($history, JSON_THROW_ON_ERROR), FILE_APPEND);
        file_put_contents('history.json', ',', FILE_APPEND);
        sleep(5);*/
//        } while (true);
             foreach ($history['messages'] as $message) {
//             print_r($message);
                if (array_key_exists('reply_to', $message)){
                    $ch_post = $Mtproto->MadelineProto->channels->getMessages(["channel" => -1001851760117, "id" => [$message["reply_to"]["reply_to_msg_id"]]]);
                    dump($ch_post["messages"][0]["message"]);
                    dump($message['message']);
                }
            }
    }
}
