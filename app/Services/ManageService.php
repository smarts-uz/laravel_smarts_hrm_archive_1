<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class ManageService
{

    public Nutgram $bot;

    public $channels_title;

    public $groups_title;

    public function handle(Nutgram $bot){

        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage('Tekshirmoqchi bo\'lgan useringizni idsini kiriting');
        });

        $bot->onText('id {user_id}', function (Nutgram $bot, $user_id) {
            if (is_numeric($user_id)){
                $this->getUser($bot, $user_id);
            }else{
                $bot->sendMessage('bu user idsi emas, id son bo\'lishi kerak');
            }
        });

        $bot->run();

    }

    public function getList(){
        $channels = setting('site.tg_channel');
        $groups = setting('site.tg_group');
        $channels_arr = explode(" ", $channels);
        $groups_arr = explode(" ", $groups);

        return [
            "channels" => $channels_arr,
            "groups" => $groups_arr,
        ];
    }

    public function getUser(Nutgram $bot, $user){
        $list = $this->getList();
        foreach ($list["channels"] as $channel) {
            $member = $bot->getChatMember( (int)$channel, $user);
            if ($member->status === 'member'){
                $title = $bot->getChat($channel)->title;
                $channels_id[] = $channel;
                $this->channels_title[] = $title;
            }
        }
        foreach ($list["groups"] as $group) {
            $member = $bot->getChatMember( (int)$group, $user);
            if ($member->status === 'member'){
                $title = $bot->getChat($group)->title;
                $groups_id[] = $group;
                $this->groups_title[] = $title;

            }
        }

        $this->Addbutton($bot);
    }

    public function Addbutton(Nutgram $bot)
    {
        $kb = ['reply_markup' =>
            ['keyboard' => [

            ], 'resize_keyboard' => true]
        ];
        foreach ($this->channels_title as $item) {
            $kb["reply_markup"]["keyboard"][] = [

                ['text' => $item],
                ['text' => '❌'],

            ];

        }
        foreach ($this->groups_title as $item) {
            $kb["reply_markup"]["keyboard"][] = [

                ['text' => $item],
                ['text' => '❌'],

            ];

        }
        $bot->sendMessage("Выберите одно из следующих", $kb);
    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('MANAGER_BOT_TOKEN'));
    }
}
