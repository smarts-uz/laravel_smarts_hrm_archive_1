<?php

namespace App\Services;

use danog\MadelineProto\bots;
use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

class ManageService
{

    public Nutgram $bot;

    public $channels_title;

    public $channels_id;

    public $channels_invite_link;

    public $groups_title;

    public $groups_id;

    public $groups_invite_link;

    public $cache;

    public $error;

    public $error_descr;

    public function handle(Nutgram $bot)
    {
        $bot->onMessageType(MessageTypes::LEFT_CHAT_MEMBER, function (Nutgram $bot) {
            $chat_id = $bot->chatId();
            $msg_id = $bot->messageId();
            $bot->deleteMessage($chat_id, $msg_id);
        });

        $bot->onText('/start', function (Nutgram $bot) {
            $bot->sendMessage('Enter the id of the user you want to verify');
        });

        $bot->onText('{user_id}', function (Nutgram $bot, $user_id) {
            if (is_numeric($user_id)) {
                Cache::put('user_id', $user_id);
                $user = $this->getUser($bot, $user_id);

                if ($this->error === 'error') {
                    $bot->sendMessage($this->error_descr);
                } else if ($this->error === 'not which one'){
                    $bot->sendMessage($this->error_descr);
                    $this->Addbutton($user);
                }else{
                    $this->Addbutton($user);
                }
            } else if ($user_id !== '/start' && $user_id !== 'Channels ❌' && $user_id !== 'Groups ❌' && $user_id !== 'All ❌') {
                $bot->sendMessage('I didn\'t understand you, please enter user id');
            }
        });

        $bot->onText('Channels ❌', function (Nutgram $bot) {
            if (Cache::get('user_id') !== null) {
                $user_id = Cache::get('user_id');
                $this->delFromChannel($bot, $user_id);
            } else {
                $bot->sendMessage('The first enter user id, please!');
            }
        });

        $bot->onText('Groups ❌', function (Nutgram $bot) {
            if (Cache::get('user_id') !== null) {
                $user_id = Cache::get('user_id');
                $this->delFromGroup($bot, $user_id);
            } else {
                $bot->sendMessage('The first enter user id, please!');
            }
        });

        $bot->onText('All ❌', function (Nutgram $bot) {
            if (Cache::get('user_id') !== null) {
                $user_id = Cache::get('user_id');
                $this->delFromChannel($bot, $user_id);
                $this->delFromGroup($bot, $user_id);
            } else {
                $bot->sendMessage('The first enter user id, please!');
            }
        });

        $bot->onException(function (Nutgram $bot, \Throwable $exception) {
            echo $exception->getMessage();
            $bot->sendMessage($exception->getMessage());
        });

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot" . env('DROPPER_BOT_TOKEN') . "/getWebhookInfo");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $output = curl_exec($ch);
                $output = json_decode($output);
                if ($output->result->url !== '') {
                    $bot->run();
                }

                curl_close($ch);

    }

    public function getList()
    {
        $channels = setting('site.tg_channel');
        $groups = setting('site.tg_group');
        $channels_arr = explode("\r\n", $channels);
        $groups_arr = explode("\r\n", $groups);

        foreach ($channels_arr as $index => $item) {
            $channels_arr[$index] = str_replace([' ', ',', '.'], '', $item);
        }

        foreach ($groups_arr as $index => $item) {
            $groups_arr[$index] = str_replace([' ', ',', '.'], '', $item);
        }

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
                if ($key) {
                    $member = $bot->getChatMember((int)$key, $user);

                    if ($member->status === 'member' || $member->status === 'admin') {
                        $title = $bot->getChat($key)->title;
                        $invite_link = $bot->getChat($key)->invite_link;
                        if ($chats === 'channels') {
                            $this->channels_id[] = $key;
                            $this->channels_title[] = $title;
                            $this->channels_invite_link[] = $invite_link;
                        } else {
                            $this->groups_id[] = $key;
                            $this->groups_title[] = $title;
                            $this->groups_invite_link[] = $invite_link;
                        }
                    }
                }
                if ($list["channels"][0] === "" && $list["groups"][0]=== ""){
                    $this->error_descr = "Not added channels & groups id from admin panel";
                    $this->error = "error";
                }
                else if ($type[0] === ""){
                    $this->error_descr = "Not added $chats id from admin panel";
                    $this->error = "not which one";
                }

            }
        }
        return $bot;
    }

    public function delFromChannel(Nutgram $bot, $user_id)
    {
        $this->getUser($bot, $user_id);
        if ($this->channels_id !== null) {
            foreach ($this->channels_id as $item) {
                $bot->banChatMember($item, $user_id);
            }
            $bot->sendMessage('User deleted from channels');

            $this->channels_title = null;
            $this->groups_title = null;
        } else {
            $bot->sendMessage('The user does not exist on any channel');
        }

    }

    public function delFromGroup(Nutgram $bot, $user_id)
    {
        $this->getUser($bot, $user_id);
        if ($this->groups_id !== null) {
            foreach ($this->groups_id as $item) {
                $bot->banChatMember($item, $user_id);

            }
            $bot->sendMessage('User deleted from groups');

            $this->channels_title = null;
            $this->groups_title = null;
        } else {
            $bot->sendMessage('The user does not exist on any group');
        }

    }

    public function Addbutton(Nutgram $bot)
    {
        $str = '';
        if ($this->channels_title !== null) {
            $str .= "Channels" . "\n" . "\n";
            for ($i = 0, $iMax = count($this->channels_title); $i < $iMax; $i++) {
                $str .= $i + 1 . '. ' . $this->channels_title[$i] . "  " . $this->channels_invite_link[$i] . "\n";
            }
        }
        $str .= '___________________________________________' . "\n" . "\n";

        if ($this->groups_title !== null) {
            $str .= "Groups" . "\n" . "\n";
            for ($i = 0, $iMax = count($this->groups_title); $i < $iMax; $i++) {
                $str .= $i + 1 . '. ' . $this->groups_title[$i] . "  " . $this->groups_invite_link[$i] . "\n";
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
        $this->channels_title = null;
        $this->groups_title = null;

    }

    public function __construct()
    {
        $this->bot = new Nutgram(env('DROPPER_BOT_TOKEN'));
    }
}
