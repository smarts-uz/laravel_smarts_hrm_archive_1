<?php

namespace App\Services\DinnerBot;

use danog\MadelineProto\bots;
use SergiX44\Nutgram\Nutgram;

class DinnerBotService
{
    public Nutgram $bot;

    public function handle(Nutgram $bot)
    {
        $bot->onCommand('/start', function (Nutgram $bot) {
            $bot->sendMessage('Bu bot orqali siz SmartSoftwareda tushlik buyurtma qilishingiz mumkin');
        });

        $bot->onMessage(function (Nutgram $bot) {
            $message = $bot->message();
            if (property_exists($message, 'forward_from_chat') && $message->forward_from_chat !== null) {
                if ($message->forward_from_chat->id === -1001652566931) {
                    echo gettype($message->forward_from_chat->id);
                    file_put_contents('message.json', json_encode($message, JSON_THROW_ON_ERROR), FILE_APPEND);
                    print_r(json_encode($message, JSON_THROW_ON_ERROR));
                    $msg_caption = $message->caption;
                    $msg_caption = str_replace(["📔", "💰", "\n", " "]);
                    $bot->sendMessage($msg_caption);
                }
            }
        });
    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('DINNER_BOT_TOKEN'));
    }
}
