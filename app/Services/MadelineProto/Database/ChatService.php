<?php

namespace App\Services\MadelineProto\Database;

use App\Models\TgChat;
use danog\MadelineProto\API;

class ChatService
{
    public $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
    }

    public function fill(): void
    {
        $channels_id = TgChat::pluck('tg_id');

        foreach ($channels_id as $channel_id) {

            $this->getChanel($channel_id);
        }
    }

    /**
     * @return API
     */
    public function getChanel($channel_id)
    {
        for ($i = 1; $i <= 300; $i++) {
            $item = $this->MadelineProto->channels->getMessages(["channel" => -100 . $channel_id, "id" => [45]])['messages'];
        }
    }
}
