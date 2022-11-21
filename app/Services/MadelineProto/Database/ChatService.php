<?php

namespace App\Services\MadelineProto\Database;

use App\Models\TgChat;
use App\Services\MadelineProto\MTProtoService;

class ChatService
{
    public function updateAll(){
        $MTProto = new MTProtoService();
        $chats = TgChat::where('mtproto', null)->get();
        foreach ($chats as $user){
            $temp = [];
            $chat = $MTProto->MadelineProto->getPwrChat('-100' . $user->tg_id);
            $temp['type'] = $chat['type'];
            $temp['title'] = $chat['title'];
            $temp['restricted'] = $chat['restricted'];
            $temp['access_hash'] = $chat['access_hash'];
            $temp['signatures'] = $chat['signatures'];
            $temp['read_inbox_max_id'] = $chat['read_inbox_max_id'];
            $temp['read_outbox_max_id'] = $chat['read_outbox_max_id'];
            $temp['bot_info'] = array_key_exists('bot_info', $chat) ? json_encode($chat['bot_info']) : null;
            $temp['notify_settings'] = json_encode($chat['notify_settings']);
            $temp['can_set_stickers'] = $chat['can_set_stickers'];
            $temp['can_view_participants'] = $chat['can_view_participants'];
            $temp['can_set_username'] = $chat['can_set_username'];
            $temp['participants_count'] = $chat['participants_count'];
            $temp['admins_count'] = $chat['admins_count'];
            $temp['kicked_count'] = $chat['kicked_count'];
            $temp['banned_count'] = $chat['banned_count'];
            $temp['pinned_msg_id'] = $chat['pinned_msg_id'];
            $temp['about'] = $chat['about'];
            $temp['can_view_stats'] = $chat['can_view_stats'];
            $temp['online_count'] = $chat['online_count'];
            $temp['invite'] = $chat['invite'];
            $temp['participants'] = array_key_exists('participants', $chat) ? json_encode($chat['participants']) : null;
            $temp['mtproto'] = json_encode($chat);
            $post = TgChat::find($user->id);
            $post->update($temp);
        }
    }
}
