<?php

namespace App\Console\Commands;

use App\Http\Controllers\Controller;
use App\Services\MadelineProto\MTProtoService;
use App\Services\SearchService;
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
        $Search = new SearchService();
        $g_history = $Mtproto->MadelineProto->messages->getHistory(["peer" => -1001851760117]);
        foreach ($g_history['messages'] as $message) {
            if (array_key_exists('reply_to', $message)) {
                $g_post = $Mtproto->MadelineProto->channels->getMessages(["channel" => -1001851760117, "id" => [$message["reply_to"]["reply_to_msg_id"]]]);
                $g_post_msg = $g_post["messages"][0]["message"];
                if (strpos($g_post_msg, "#Task")) {
                    $ch_post_id = $Search->searchMessage(-1001715385949, $g_post_msg);
                    $g_post_msg;  /* gruppadig kanal posti #task borligi */
                    $message['message'];  /* uni commenti */
                    if (array_key_exists("from_id", $g_history['messages'][0])) {
//                        dump($g_history['messages'][0]['message']);
//                        dump($message["from_id"]["user_id"]);
                        switch (true) {
                            case ($g_history['messages'][0]['message'] == '#Redy' && $g_history['messages'][0]["from_id"]["user_id"] == 5466804391 && !strpos($g_post_msg, "#NeedTests")):
                                $ch_post_id; /* kanal post id */
                                $ch_post = $Mtproto->MadelineProto->channels->getMessages(["channel" => -1001715385949, "id" => [$ch_post_id]]);
                                $ch_post;
                                $editted = $g_post_msg . "\n#NeedTests";
                                $Mtproto->MadelineProto->messages->editMessage(["peer" => -1001715385949, "id" => $ch_post_id, "message" => $editted]);
                                break;
                        }
                    }
                }
            }
        }
    }
}
