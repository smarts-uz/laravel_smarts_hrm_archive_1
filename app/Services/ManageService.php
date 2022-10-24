<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class ManageService
{

    public Nutgram $bot;

    public $channels_title;

    public $channels_id;

    public $groups_title;

    public $groups_id;

    public function handle(Nutgram $bot)
    {

        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage('Tekshirmoqchi bo\'lgan useringizni idsini kiriting');
        });

        $bot->onText('id {user_id}', function (Nutgram $bot, $user_id) {
            if (is_numeric($user_id)) {
                $user = $this->getUser($bot, $user_id);
                $this->Addbutton($user);
            } else {
                $bot->sendMessage('bu user idsi emas, id son bo\'lishi kerak');
            }
        });

        $bot->onText('Channels ❌', function (Nutgram $bot) {
            $this->delFromChannel($bot, );
        });

        $bot->onText('Groups ❌', function (Nutgram $bot) {
            $this->delFromGroup($bot, );
        });


        $bot->run();

    }

    public function getList()
    {
        $channels = setting('site.tg_channel');
        $groups = setting('site.tg_group');
        $channels_arr = explode(" ", $channels);
        $groups_arr = explode(" ", $groups);

        return [
            "channels" => $channels_arr,
            "groups" => $groups_arr,
        ];
    }

    public function getUser(Nutgram $bot, $user)
    {
        $list = $this->getList();
        foreach ($list as $chats => $type) {

            foreach ($type as $chat => $key) {
                $member = $bot->getChatMember((int)$key, $user);
                if ($member->status === 'member' || $member->status === 'creator') {
                    $title = $bot->getChat($key)->title;
                    if ($chats === 'channels'){
                        $this->channels_id[] = $key;
                        $this->channels_title[] = $title;
                    }else{
                        $this->groups_id[] = $key;
                        $this->groups_title[] = $title;
                    }
                }
            }
        }
        /*foreach ($list["groups"] as $group) {
            $member = $bot->getChatMember((int)$group, $user);
            echo 'gruppa';
            if ($member->status === 'member' || $member->status === 'creator') {
                $title = $bot->getChat($group)->title;
                $this->groups_id[] = $group;
                $this->groups_title[] = $title;

            }
        }*/
        return $bot;
    }

    public function delFromChannel(Nutgram $bot, $user_id){
        $this->getUser($bot,$user_id);
        foreach ($this->channels_id as $item) {
            $bot->banChatMember($item,$user_id);
        }
        $bot->sendMessage('User deleted from channels');
    }

    public function delFromGroup(Nutgram $bot, $user_id){
        $this->getUser($bot,$user_id);
        foreach ($this->groups_id as $item) {
            $bot->banChatMember($item,$user_id);
        }
        $bot->sendMessage('User deleted from groups');
    }

    public function Addbutton(Nutgram $bot)
    {
        $str = '';
        if ($this->channels_title !== null) {
            for ($i = 0, $iMax = count($this->channels_title); $i < $iMax; $i++) {
                $str .= $i + 1 . '. ' . $this->channels_title[$i] . " (Channel)" . "\n";
            }
        }
        $str .= '_____________________________
        ' . "\n";

        if ($this->groups_title !== null) {
            for ($i = 0, $iMax = count($this->groups_title); $i < $iMax; $i++) {
                $str .= $i + 1 . '. ' . $this->groups_title[$i] . " (Group)" . "\n";
            }
        }

        $kb = ['reply_markup' =>
            ['keyboard' => [

            ], 'resize_keyboard' => true]
        ];
        if ($this->channels_title !== null) {
            $kb["reply_markup"]["keyboard"][] = [

                ['text' => 'Channels ❌'],
            ];
        }
        if ($this->groups_title !== null) {
            $kb["reply_markup"]["keyboard"][] = [

                ['text' => 'Groups ❌'],

            ];
        }
        if ($this->groups_title !== null || $this->channels_title !== null) {
            $kb["reply_markup"]["keyboard"][] = [

                ['text' => 'All ❌'],

            ];

        }
        $bot->sendMessage($str, $kb);
    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('MANAGER_BOT_TOKEN'));
    }
}
