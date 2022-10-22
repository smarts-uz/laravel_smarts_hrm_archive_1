<?php

namespace App\Services;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ManageService extends InlineMenu
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
            $member = $bot->getChatMember( (int)$channel, $user);
            if ($member->status === 'member'){
                $title = $bot->getChat($channel)->title;
                $channels_id[] = $channel;
                $channels_title[] = $title;
            }
        }
        foreach ($list["groups"] as $group) {
            $member = $bot->getChatMember( (int)$group, $user);
            if ($member->status === 'member'){
                $title = $bot->getChat($group)->title;
                $groups_id[] = $group;
                $groups_title[] = $title;

            }
        }

        $this->Addbutton($bot);

        dd($channels_title, $groups_title);
        /*$this->menuText('Choose a color:')->addButtonRow(InlineKeyboardButton::make('Red', callback_data: 'red@handleColor'))->addButtonRow(InlineKeyboardButton::make('Green', callback_data: 'green@handleColor'))->addButtonRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow@handleColor'))->orNext('none')->showMenu();*/
    }

    public function Addbutton(Nutgram $bot)
    {
        $this->menuText('Choose a color:', ["chat_id" => 1307688882])
            ->addButtonRow(InlineKeyboardButton::make('Red', callback_data: 'red@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Green', callback_data: 'green@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow@handleColor'))
            ->orNext('none')
            ->showMenu();
    }

    public function handleColor(Nutgram $bot)
    {
        $color = $bot->callbackQuery()->data;
        $this->menuText("Choosen: $color!")
            ->showMenu();
    }

    public function none(Nutgram $bot)
    {
        $bot->sendMessage('Bye!');
        $this->end();
    }

    public function __construct()
    {
        parent::__construct();
        $this->bot = new Nutgram('5405829088:AAEIArJ7zMIDjOqBEQyCmOnpQygyjkV09YQ');
    }
}
