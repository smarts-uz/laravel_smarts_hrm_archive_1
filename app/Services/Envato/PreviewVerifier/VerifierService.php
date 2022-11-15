<?php

namespace App\Services\Envato\PreviewVerifier;

use App\Services\Envato\EnvatoService;
use App\Services\MadelineProto\MTProtoService;
use Exception;

class VerifierService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function verifier($start, $end = null)
    {
        $MTProto = new MTProtoService();

        if ($end != null) {
            for ($i = $start; $i < $end; $i++) {
                $line = 'https://t.me/c/' . substr(env("CHANNEL_ID"), 4) . '/' . $i;
                try{
                    $message = $this->MTProto->MadelineProto->messages->getDiscussionMessage(["peer" => env("CHANNEL_ID"), 'msg_id' => $i]);
                }catch (Exception $e){
                    continue;
                }
                $comments = $this->MTProto->getComments($line);
                $link = $envato->getLink($message['messages'][0]['media']['webpage']['url']);
                if (count($comments) == 0) {
                    $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                        "message" => $link[0] . "\r\n\r\n#post_url",
                        'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                    try {
                        $MTProto->MadelineProto->messages->sendMedia(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "media" => ['_' => 'inputMediaUploadedDocument', 'file' => $link[0]], "message" => '#post_file',
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                    } catch (Exception $e) {
                        $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "message" => ".\r\n\r\n#post_file",
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
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
                                'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                        }
                    }
                    if ($post_url == 0) {
                        $MTProto->MadelineProto->messages->sendMessage(['peer' => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                            "message" => $link[0] . "\r\n\r\n#post_url",
                            'reply_to_msg_id' => (int)$message['messages'][0]['id']]);
                    }
                }
            }
        }

    }
}
