<?php

namespace App\Services\MadelineProto\Database;

use App\Models\TgChat;
use App\Models\TgChatText;
use danog\MadelineProto\API;

class ChatService
{
    public $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
    }

    protected $chatText;
    protected $tg_chat;

    public function fill(): void
    {
        $this->tg_chat = new TgChat;
        $this->chatText = new TgChatText;
        $channels_id = $this->tg_chat->pluck('tg_id');
        foreach ($channels_id as $channel_id) {
            $tg_id = collect($this->chatText->orderBy('tg_id')->pluck('tg_id'))->all();
            if ($tg_id !== []) {
                $end = $this->MadelineProto->getHistory(['peer' => -100 . $channel_id, 'limit' => 1]);
                $this->getChanel($channel_id, 1, $end);
            } else {
                $end = $this->MadelineProto->messages->getHistory(['peer' => -100 . $channel_id, 'limit' => 1])['messages'][0]['id'];
                $this->getChanel($channel_id, 1, $end);
            }
        }
    }

    /**
     * @return API
     */

    public function getChanel(int $channel_id, int $start, int $end)
    {
        for ($i = $start; $i <= $end; $i += 200) {
            $messages = $this->MadelineProto->channels->getMessages(["channel" => -100 . $channel_id, "id" => range($i, $end)])['messages'];
            foreach ($messages as $item) {
                $chatText = new TgChatText;
                if ($item['_'] !== 'messageEmpty') {
                    $chatText->type = $item['_'];
                    $chatText->out = $item['out'];
                    $chatText->mentioned = $item['mentioned'];
                    $chatText->media_unread = $item['media_unread'];
                    $chatText->silent = $item['silent'];
                    $chatText->post = $item['post'];
                    $chatText->from_scheduled = array_key_exists('from_scheduled', $item) ? $item['from_scheduled'] : NULL;
                    $chatText->legacy = $item['legacy'];
                    $chatText->edit_hide = array_key_exists('edit_hide', $item) ? $item['edit_hide'] : NULL;
                    $chatText->pinned = array_key_exists('pinned', $item) ? $item['pinned'] : NULL;
                    $chatText->noforwards = array_key_exists('noforwards', $item) ? $item['noforwards'] : NULL;
                    $chatText->tg_id = $item['id'];
                    $chatText->from_id__ = array_key_exists('from_id', $item) ? $item['from_id']['_'] : NULL;
                    $chatText->from_id_user_id = array_key_exists('from_id', $item) ? $item['from_id']['user_id'] : NULL;
                    $chatText->peer_id__ = $item['peer_id']['_'];
                    $chatText->peer_id_channel_id = $item['peer_id']['channel_id'];
                    $chatText->reply_to__ = array_key_exists('reply_to', $item) ? $item['reply_to']['_'] : NULL;
                    $chatText->reply_to_reply_to_scheduled = array_key_exists('reply_to', $item) ? $item['reply_to']['reply_to_scheduled'] : NULL;
                    $chatText->reply_to_reply_to_msg_id = array_key_exists('reply_to', $item) ? $item['reply_to']['reply_to_msg_id'] : NULL;
                    $chatText->date = date("Y-m-d H:i:s", $item['date']);
                    $chatText->message = array_key_exists('message', $item) ? $item['message'] : NULL;
                    $chatText->media__ = array_key_exists('media', $item) ? $item['media']['_'] : NULL;
                    $chatText->media = array_key_exists('media', $item) ? json_encode($item['media']) : NULL;
                    $chatText->action__ = array_key_exists('action', $item) ? $item['action']['_'] : NULL;
                    $chatText->action_title = array_key_exists('action', $item) ? $item['action']['title'] : NULL;
                    $chatText->mtproto = json_encode($item);
                    $chatText->save();
                    //file_put_contents('c.json', json_encode($messages_Messages));
                }
            }
        }
    }
}
