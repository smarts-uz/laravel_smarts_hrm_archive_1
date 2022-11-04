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

    public function searchForMessage($txt_data, $titles = [])
    {
        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
        $MTProto = new MTProtoService();
        $text = $this->folders($txt_data, $titles);
        print_r($text);
        print_r(PHP_EOL);
        $message_id = $this->searchMessage($txt_data[1], $text);
        if ($message_id == null || $message_id == "") {
            $bot->sendMessage($text, ['chat_id' => $txt_data[1]]);
            print_r($text);
            print_r(PHP_EOL);
            $message_id = $this->searchMessage($txt_data[1], $text);
        }
        $line = 'https://t.me/c/' . substr($txt_data[1], 4) . '/' . $message_id;
        return $line;
    }

    public function folders($txt_data, $folders = [])
    {
        if (count($folders) != 0) {
            $text = '';
            foreach (array_reverse($folders) as $title) {
                $text .= $title;
                $text .= ' | ';
            }
            $text .= $txt_data[0];
            return $text;
        } else {
            return $txt_data[0];
        }

    }
}
