<?php

namespace App\Services\MadelineProto\Database;

use App\Models\TgUser;
use App\Models\TgUserText;
use App\Services\MadelineProto\MTProtoService;
use Mockery\Exception;

class UserService
{
    public function updateAll()
    {
        $MTProto = new MTProtoService();
        $users = TgUser::where('mtproto', null)->get();
        foreach ($users as $user) {
            $temp = [];
            $chat = $MTProto->MadelineProto->getPwrChat($user->tg_id);
            $temp['type'] = $chat['type'];
            $temp['first_name'] = $chat['first_name'];
            $temp['last_name'] = array_key_exists('last_name', $chat) ? $chat['last_name'] : null;
            $temp['username'] = array_key_exists('username', $chat) ? $chat['username'] : null;
            $temp['status'] = $chat['status']['_'];
            $temp['access_hash'] = $chat['access_hash'];
            $temp['phone'] = array_key_exists('phone', $chat) ? $chat['phone'] : null;
            $temp['bot_nochats'] = $chat['bot_nochats'];
            $temp['phone_calls_available'] = $chat['phone_calls_available'];
            $temp['phone_calls_private'] = $chat['phone_calls_private'];
            $temp['common_chats_count'] = $chat['common_chats_count'];
            $temp['can_pin_message'] = $chat['can_pin_message'];
            $temp['notify_settings'] = json_encode($chat['notify_settings']);
            $temp['photo'] = array_key_exists('photo', $chat) ? json_encode($chat['photo']) : null;
            $temp['mtproto'] = json_encode($chat);
            $post = TgUser::find($user->id);
            $post->update($temp);
        }
    }

    public function updateMessages()
    {
        $MTProto = new MTProtoService();
        $users = TgUser::pluck('tg_id');
        foreach ($users as $user){
            $cha = TgUserText::where('peer_id_user_id', $user)->latest('tg_id', 'desc')->first();
            $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => (int)$user, 'limit' => 50]);
            $mess = $messages['messages'];
            for($i=$cha->tg_id; $i<$mess[0]['id']; $i++){
                $message = $MTProto->MadelineProto->messages->getMessages(['channel_id' => $user, 'id' => [$i]]);

                $temp = [];
                $temp['_'] = $message['_'];
                $temp['out'] = $message['out'];
                $temp['mentioned'] = $message['mentioned'];
                $temp['media_unread'] = $message['media_unread'];
                $temp['silent'] = $message['silent'];
                $temp['post'] = $message['post'];
                $temp['from_scheduled'] = array_key_exists('from_scheduled', $message) ? $message['from_scheduled'] : null;
                $temp['legacy'] = $message['legacy'];
                $temp['edit_hide'] = array_key_exists('edit_hide', $message) ? $message['edit_hide'] : null;
                $temp['pinned'] = array_key_exists('pinned', $message) ? $message['pinned'] : null;
                //$temp['noforwards'] = array_key_exists('noforwards', $message) ? $message['noforwards'] : null;
                $temp['tg_id'] = (int)$message['id'];
                $temp['peer_id_'] = $message['peer_id']['_'];
                $temp['peer_id_user_id'] = $message['peer_id']['user_id'];
                $temp['date'] = (string)$message['date'];
                //$temp['message'] = array_key_exists('message', $message) ? $message['message'] : '';
                $temp['reply_to_'] = array_key_exists('reply_to', $message) ? $message['reply_to']['_'] : null;
                $temp['reply_to_reply_to_scheduled'] = array_key_exists('reply_to', $message) ? $message['reply_to']['reply_to_scheduled'] : null;
                $temp['reply_to_reply_to_msg_id'] = array_key_exists('reply_to', $message) ? $message['reply_to']['reply_to_msg_id'] : null;
                $temp['mtproto'] = json_encode($message);
                TgUserText::create($temp);
            }

            print_r($cha);
            print_r(PHP_EOL);

            print_r($user);
            print_r(PHP_EOL);
            print_r($cha->tg_id);
            print_r(PHP_EOL);
            /*foreach ($mess as $message) {
                $temp = [];
                $temp['_'] = $message['_'];
                $temp['out'] = $message['out'];
                $temp['mentioned'] = $message['mentioned'];
                $temp['media_unread'] = $message['media_unread'];
                $temp['silent'] = $message['silent'];
                $temp['post'] = $message['post'];
                $temp['from_scheduled'] = array_key_exists('from_scheduled', $message) ? $message['from_scheduled'] : null;
                $temp['legacy'] = $message['legacy'];
                $temp['edit_hide'] = array_key_exists('edit_hide', $message) ? $message['edit_hide'] : null;
                $temp['pinned'] = array_key_exists('pinned', $message) ? $message['pinned'] : null;
                //$temp['noforwards'] = array_key_exists('noforwards', $message) ? $message['noforwards'] : null;
                $temp['tg_id'] = (int)$message['id'];
                $temp['peer_id_'] = $message['peer_id']['_'];
                $temp['peer_id_user_id'] = $message['peer_id']['user_id'];
                $temp['date'] = (string)$message['date'];
                //$temp['message'] = array_key_exists('message', $message) ? $message['message'] : '';
                $temp['reply_to_'] = array_key_exists('reply_to', $message) ? $message['reply_to']['_'] : null;
                $temp['reply_to_reply_to_scheduled'] = array_key_exists('reply_to', $message) ? $message['reply_to']['reply_to_scheduled'] : null;
                $temp['reply_to_reply_to_msg_id'] = array_key_exists('reply_to', $message) ? $message['reply_to']['reply_to_msg_id'] : null;
                $temp['mtproto'] = json_encode($message);
                TgUserText::create($temp);
            }*/
        }


    }

}
