<?php

namespace App\Services\RemoveJoinLeave;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

class RemoveJoinLeaveService
{
    public Nutgram $bot;

    public function handle(Nutgram $bot){
        $bot->onMessageType(MessageTypes::LEFT_CHAT_MEMBER, function (Nutgram $bot) {
            $chat_id = $bot->chatId();
            $msg_id = $bot->messageId();
            $bot->deleteMessage($chat_id, $msg_id);
        });
        $bot->onMessageType(MessageTypes::NEW_CHAT_MEMBERS, function (Nutgram $bot) {
            $chat_id = $bot->chatId();
            $msg_id = $bot->messageId();
            $bot->deleteMessage($chat_id, $msg_id);
        });
    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('REMOVE_JOIN_BOT_TOKEN'));
    }
}
