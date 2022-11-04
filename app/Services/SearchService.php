<?php


namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class SearchService
{

    public MTProtoService $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    function searchMessage($channel, $searched)
    {

        $messages = [];
        $offset_id = 0;
        $limit = 100;

        do {
            $messages_Messages = $this->MTProto->MadelineProto->messages->getHistory(['peer' => $channel, 'offset_id' => $offset_id, 'offset_date' => 0, 'add_offset' => 0, 'limit' => $limit, 'max_id' => 0, 'min_id' => 0, 'hash' => 0]);
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
        foreach ($messages['message'] as $message => $key) {
            if ($key === $searched) {
                return $messages['id'][$message];
            }
        }
    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
