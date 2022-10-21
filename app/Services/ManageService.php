<?php

namespace App\Services;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

class ManageService
{
    public Nutgram $bot;
    
    public function handle(Nutgram $bot){
        $bot->onCommand('start', function (Nutgram $bot) {
            return $bot->sendMessage('Hello, world!');
        })->description('The start command!');
        $bot->run();
    }

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
                $title ='channel: '.$this->bot->getChat($channel)->title;
                $channels_id[] = $channel;
                $channels_title[] = $title;
            }
        }
        foreach ($list["groups"] as $group) {
            $member = $this->bot->getChatMember( (int)$group, $user);
            if ($member->status === 'member'){
                $title = 'group: '.$this->bot->getChat($group)->title;
                $groups_id[] = $group;
                $groups_title[] = $title;

            }
        }
        echo 'channels';
        dump($channels_id);
        dump($channels_title);
        echo "groups";
        dump($groups_id);
        dump($groups_title);
    }

    public function __construct()
    {
        $this->bot = new Nutgram('5405829088:AAEIArJ7zMIDjOqBEQyCmOnpQygyjkV09YQ');
    }
}
