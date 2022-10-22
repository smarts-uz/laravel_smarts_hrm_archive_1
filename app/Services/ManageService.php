<?php

namespace App\Services;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

class ManageService
{
    
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


        /*$bot->onCommand('start', function (Nutgram $bot) {
            return $bot->sendMessage('Hello, world!');
        })->description('The start command!');
        $bot->run();*/
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
                $channels_title = '';
            $member = $bot->getChatMember( (int)$channel, $user);
            if ($member->status === 'member'){
                $Chtitle = $bot->getChat($channel)->title;
                $channels_id[] = $channel;
                $channels_title .= "$Chtitle | ";
            }
        }
        foreach ($list["groups"] as $group) {
                $groups_title = '';
            $member = $bot->getChatMember( (int)$group, $user);
            if ($member->status === 'member'){
                $Gtitle = $bot->getChat($group)->title;
                $groups_id[] = $group;
                $groups_title .= "$Gtitle | ";

            }
        }
        /*dump($groups_title);
        dump($channels_title);*/
        $bot->sendMessage("Kanallarda: $channels_title");
        $bot->sendMessage("Gruppalarda: $groups_title");
        /*dump($channels_id);
        dump($channels_title);
        echo "groups";
        dump($groups_id);
        dump($groups_title);*/
    }

    public function __construct()
    {
        $this->bot = new Nutgram('5405829088:AAEIArJ7zMIDjOqBEQyCmOnpQygyjkV09YQ');
    }
}
