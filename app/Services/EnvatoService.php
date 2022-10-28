<?php

namespace App\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

class EnvatoService
{
    public $MadelineProto;

    public function __construct()
    {
        $settings = new Settings;

        $this->MadelineProto = new API('D:/envato/index.madeline');
        $this->MadelineProto->start();
    }

    public function run($channel, $comment)
    {
        $messages_coments = $this->MadelineProto->messages->getHistory([
            'peer' => $comment,
            'limit' => 9999,
            'max_id' => 999999,]);

        $comments = [];
        foreach ($messages_coments['messages'] as $key => $item) {
            if (array_key_exists('reply_to',$item)) {
                $comments[$item['id']] = $item['message'];
            }
        }

        $limit = 10;
        $messages_Messages = $this->MadelineProto->messages->getHistory([
            'peer' => $channel,
            'limit' => $limit,
            'max_id' => 999999,]);

        $posts = [];
        foreach ($messages_Messages['messages'] as $key => $item) {
            if (array_key_exists('media',$item) and $item['replies']['replies'] !== 0) {
                $posts[$key]['url'] = $item['media']['webpage']['url'];
                //$posts[$key]['channel_id'] = $item['replies']['channel_id'];
                $posts[$key]['max_id'] = $item['replies']['max_id'];
                /*foreach ($comments['2333'] as $com) {
                    var_dump($com);
                    var_dump($item['replies']['max_id']);
                }*/
            }

        }

        file_put_contents('c.json', json_encode($posts));
        file_put_contents('d.json', json_encode($comments));
        file_put_contents('b.json', json_encode($messages_coments['messages']));
    }
}
