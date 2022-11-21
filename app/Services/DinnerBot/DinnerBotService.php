<?php

namespace App\Services\DinnerBot;

use danog\MadelineProto\bots;
use SergiX44\Nutgram\Nutgram;

class DinnerBotService
{
    public Nutgram $bot;

    public function handle(Nutgram $bot){
        $bot->onCommand('/start', function (Nutgram $bot){
            $bot->sendMessage('Bu bot orqali siz SmartSoftwareda tushlik buyurtma qilishingiz mumkin');
        });
    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('DINNER_BOT_TOKEN'));
    }
}
