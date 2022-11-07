<?php


namespace App\Services\Envato;


use App\Services\MadelineProto\MTProtoService;

class SearchService
{
    public $MProto;

    public function sendMedia($url, $chat_id, $descr)
    {
        $this->MProto->messages->sendMedia(["peer" => $chat_id, "media" => ['_' => 'inputMediaUploadedDocument', 'file' => $url], "message" => $descr]);
    }

    public function getLink($chat_id, $from, $to = 0)
    {
        $messages = [];
        $offset_id = 0;
        $limit = 100;

        do {
            $messages_Messages = $this->MProto->messages->getHistory(['peer' => $chat_id, 'offset_id' => $offset_id, 'offset_date' => 0, 'add_offset' => 0, 'limit' => $limit, 'max_id' => $to, 'min_id' => $from, 'hash' => 0]);

            if (count($messages_Messages['messages']) == 0) break;

            foreach ($messages_Messages['messages'] as $message) {
                if (array_key_exists('message', $message)) {

                    $messages["message"][] = $message["message"];
                    $messages["id"][] = $message["id"];

                }
            }

            $offset_id = end($messages_Messages['messages'])['id'];
            sleep(2);
        } while (true);
        print_r($messages);
    }

    public function getMessages($chat_id, $from, $to)
    {
        $messages = [];
        for ($i = $from; $i <= $to; $i++) {
            $messages[] = $this->MProto->channels->getMessages(["channel" => $chat_id, "id" => [$i]]);
        }
        return $messages;
    }

    public function getDiscussion($channel_id, $msg_id){
        return $this->MProto->messages->getDiscussionMessage(["peer" => $channel_id, "msg_id" => $msg_id]);
    }

    public function __construct()
    {
        $this->MProto = new MTProtoService();
    }
}
