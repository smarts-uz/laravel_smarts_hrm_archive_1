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

    public function getComments($url, $id)
    {
            $messages = $this->MadelineProto->messages->getReplies(['peer' => -100 . $url, 'msg_id' => $id]);
            return $messages['messages'];
    }
    public function Previews($start, $end){
        $start = explode("/", $start);
        $end = explode("/", $end);
        $count = $end[5] - $start[5];
        for ($i = 0; $i <= $count; $i++) {
            try {
                $comments[$start[5] - $count] = $this->getComments($start[4], $end[5] - $i);
            } catch (Exception $e) {
                dump($e->getMessage());
            }

        }
        file_put_contents('a.json', json_encode($comments));
    }
}
