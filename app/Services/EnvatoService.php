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

    public function getLink($postLink)
    {
        // 1. инициализация
        $ch = curl_init();
        // 2. указываем параметры, включая url
        curl_setopt($ch, CURLOPT_URL, $postLink);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)');
        // 3. получаем HTML в качестве результата
        $subject = curl_exec($ch);
        // 4. закрываем соединение
        curl_close($ch);
        // 5. получаем url ссылки
        preg_match(
        '#(https://video-previews[a-z-_/\.0-9]+)|((?<=src=")https://elements-cover-images[a-zA-Z-_/\.0-9\?%&;=]+)#',
            preg_replace('#amp;#', '', $subject), $matches);
        return $matches;
    }

    public function getComments($start, $end, $posts) {
        $count = $end[5] - $start[5];
        $comments = [];
        for ($i = 0; $i <= $count; $i++) {
            if (array_key_exists($start[5] + $i, $posts)) {
                $comments[$start[5] + $i] = $this->MadelineProto->messages->getReplies(
                    ['peer' => -100 . $start[4],
                        'msg_id' => $start[5] + $i])['messages'];

            }
        }
        return $comments;
    }

    public function getPostId($channel_id) {
        $posts = [];
        $offset_id = 0;
        do {
            $messages_Messages = $this->MadelineProto->messages->getHistory([
                'peer' => '-100' . $channel_id,
                'offset_id' => $offset_id,
                'limit' => 100,
                'max_id' => 99999
            ]);

            if (count($messages_Messages['messages']) == 0) break;

            foreach ($messages_Messages['messages'] as $key => $item) {
                if (array_key_exists('media', $item)) {
                    if (array_key_exists('webpage', $item['media'])) {
                        $posts[$item['id']] = $item['media']['webpage']['url'];
                    }
                }
            }

            $offset_id = end($messages_Messages['messages'])['id'];

            sleep(2);
        } while (true);
        return $posts;
    }

    public function sendlink($posts, $comments) {
        foreach ($comments as $key => $postComment) {
            foreach ($postComment as $comment) {
                if (array_key_exists('message', $comment)) {
                    preg_match('/#(video-previews)|(elements-cover-images)#/i', $comment, $match);
                    if (!is_array($match)) {

                    }
                }
            }
        }
    }

    public function Previews($start, $end){
        $start = explode("/", $start);
        $posts = $this->getPostId($start[4]);
        $end = explode("/", $end);
        $comments = $this->getComments($start, $end, $posts);
        file_put_contents('a.json', json_encode($comments));
        file_put_contents('b.json', json_encode($posts));
    }
}
