<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\MTProtoService;
use App\Services\NutgramService;
use App\Services\PythonService;
use App\Services\SearchService;
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
        $envato = new \App\Services\EnvatoService();
        $search = new SearchService();

        $offset = 'https://t.me/c/1807426588/532';
        $end = 'https://t.me/c/1807426588/536';

        if ($end == null) {
            return;
        } else {
            for ($i = (int)explode('/', $offset)[5]; $i <= (int)explode('/', $end)[5]; $i++) {
                print_r(substr($offset, 0, -strlen(explode('/', $offset)[5])));
                print_r($i);
                try {
                    $comments = $MTProto->getComments(substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
                } catch (Exception $e) {
                    print_r($e->getMessage());
                    continue;
                }

                $split = explode("/", substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
                $replies = $MTProto->MadelineProto->messages->getHistory(['peer' => '-100' . $split[4], 'offset_id' => (int)$split[5] + 1]);
                $link = $envato->getLink($replies['messages'][0]['message']);
                $message = $MTProto->MadelineProto->messages->getDiscussionMessage(['peer' => '-100' . $split[4], 'msg_id' => (int)$split[5]]);

                if (count($comments) == 0) {
                    $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                        "message" => $link[0] . "\r\n\r\n#post_url",
                        'reply_to_msg_id' => (int)$message['messages'][0]['id']
                    ]);
                    try {
                        $MTProto->MadelineProto->messages->sendMedia(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "media" => ['_' => 'inputMediaUploadedDocument', 'file' => $link[0]], "message" => '#post_file',
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                    } catch (Exception $e) {
                        $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "message" => ".\r\n\r\n#post_file",
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']
                        ]);
                    }
                } else {
                    $post_file = 0;
                    $post_url = 0;
                    foreach ($comments as $comment) {
                        if (str_contains($comment['message'], "#post_file")) {
                            $post_file = 1;
                        }
                        if (str_contains($comment['message'], "#post_url")) {
                            $post_url = 1;
                        }
                    }
                    if ($post_file == 0) {
                        try {
                            $MTProto->MadelineProto->messages->sendMedia(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                                "media" => ['_' => 'inputMediaUploadedDocument', 'file' => $link[0]], "message" => '#post_file',
                                'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                        } catch (Exception $e) {
                            $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                                "message" => ".\r\n\r\n#post_file",
                                'reply_to_msg_id' => (int)$message['messages'][0]['id']
                            ]);
                        }
                    }
                    if ($post_url == 0) {
                        $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "message" => $link[0] . "\r\n\r\n#post_url",
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']
                        ]);
                    }
                }
            }
        }
    }
}
