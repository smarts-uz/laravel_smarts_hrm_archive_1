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

        $this->MadelineProto = new API('D:/Sessions/akbarshoh.8522/session.madeline');
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

    public function getComments($start, $end, $post_id) {
        $count = $end[5] - $start[5];
        $posts = [];

        for ($i = 0; $i <= $count; $i++) {
            if (in_array($start[5] + $i, $post_id)) {
                $posts[$start[5] + $i] = $this->MadelineProto->messages->getReplies(
                    ['peer' => -100 . $start[4],
                        'msg_id' => $start[5] + $i])['messages'];
            }
        }
        return $posts;
    }

    public function getPostId($channel_id) {
        $post_id = [];
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
                $post_id[] = $item['id'];
            }

            $offset_id = end($messages_Messages['messages'])['id'];

            sleep(2);
        } while (true);
        return $post_id;
    }

    public function sendlink() {

    }

    public function Previews($start, $end){
        $start = explode("/", $start);
        $end = explode("/", $end);
        $post_id = $this->getPostId($start[4]);
        $posts = $this->getComments($start, $end, $post_id);
        file_put_contents('a.json', json_encode($posts));
    }
}
