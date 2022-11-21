<?php

namespace App\Services\MadelineProto\Database;

use App\Models\TgChat;
use App\Models\TgChatText;
use danog\MadelineProto\API;

class ChatService
{
    public $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
    }

    protected $chatText;
    protected $tg_chat;

    public function fill(): void
    {
        $this->tg_chat = new TgChat;
        $this->chatText = new TgChatText;
        $channels_id = $this->tg_chat->pluck('tg_id');
        foreach ($channels_id as $channel_id) {
            $tg_id = collect($this->chatText->orderBy('tg_id')->pluck('tg_id'))->all();
            if ($tg_id !== []) {
                $end = $this->MadelineProto->getHistory(['peer' => $channel_id, 'limit' => 1]);
                $this->getChanel($channel_id, 1, $end);
            } else {
                $end = $this->MadelineProto->messages->getHistory(['peer' => -100 . $channel_id, 'limit' => 1])['messages'][0]['id'];
                $this->getChanel($channel_id, 1, $end);
            }
        }
    }

    /**
     * @return API
     */

    public function getChanel(int $channel_id, int $start, int $end)
    {
        for ($i = $start; $i <= $end; $i+=200) {
            $item = $this->MadelineProto->channels->getMessages(["channel" => -100 . $channel_id, "id" => range($i,$end)])['messages'];
            foreach ($item as $message) {
                if ($message['_'] !== 'messageEmpty') {
                    $this->chatText->tg_id = $item['id'];
                    $this->chatText->message = $item['message'];
                }
            }
        }
    }
}
