<?php

namespace App\Services;


use SergiX44\Nutgram\Nutgram;

class ManageService
{
    public Nutgram $bot;

    public function getList(){
        $channels = setting('site.tg_channel');
        $groups = setting('site.tg_group');
        $channels_arr = explode(" ", $channels);
        $groups_arr = explode(" ", $groups);

        $list = [
            "channels" => $channels_arr,
            "groups" => $groups_arr,
        ];

        return $list;
    }

    public function getUser($user){
        $list = $this->getList();
        foreach ($list["channels"] as $channel) {
            $member = $this->bot->getChatMember( (int)$channel, $user);
            if ($member->status === 'member'){
                $name ='channel: '.$this->bot->getChat($channel)->title;
//                $channels[] = $channel;
            }
        }
        foreach ($list["groups"] as $group) {
            $member = $this->bot->getChatMember( (int)$group, $user);
            if ($member->status === 'member'){
                dump('group: '.$this->bot->getChat($group)->title);

//                $groups[] = $group;
            }
        }
        /*echo 'channels';
        dump($channels);
        echo "groups";
        dump($groups);*/
    }

    public function __construct()
    {
        $this->bot = new Nutgram('5405829088:AAEIArJ7zMIDjOqBEQyCmOnpQygyjkV09YQ');
    }
}
