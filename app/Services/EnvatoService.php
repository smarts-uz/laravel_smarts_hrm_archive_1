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
        return $subject;
    }

    public function run($channel, $date)
    {
        $offset_id = 0;
        $limit = 100;
        $posts = [];
        $comments = [];
        do {
            $messages_Messages = $this->MadelineProto->messages->getHistory([
                'peer' => '-100' . $channel,
                'offset_date' => $date,
                'offset_id' => $offset_id,
                'limit' => $limit,
                'max_id' => 99999
            ]);

            if (count($messages_Messages['messages']) == 0) break;

            foreach ($messages_Messages['messages'] as $key => $item) {
                if (array_key_exists('media', $item) and array_key_exists('replies', $item)) {
                    if (array_key_exists('webpage', $item['media'])) {
                        $posts[$item['id']] = $item['media']['webpage']['url'];
                    }
                }
                if (array_key_exists('media', $item) and array_key_exists('reply_to', $item)) {
                    $comments[$item['reply_to']['reply_to_msg_id']][] = $item['message'];
                }
            }
            foreach ($posts as $key => $post) {
                if (array_key_exists($key, $comments)) {
                    foreach ($comments[$key] as $comment) {
                        preg_match('/#(previews)|(elements-cover-images)#/i', $comment, $match);
                        if (!is_array($match)) {
                            preg_match('#(https://video-previews[a-z-_/\.0-9]+)|((?<=src=")https://elements-cover-images[a-zA-Z-_/\.0-9\?%&;=]+)#',
                                preg_replace('#amp;#', '', $this->getLink($post)), $matches);

                            if (array_key_exists(0, $matches)) {
                                $this->MadelineProto->messages->sendMessage(
                                    ['peer' => '-100' . $channel,
                                        'message' => $matches[0],
                                        'reply_to_msg_id' => $key]);
                            }
                        }
                    }
                } else {
                    preg_match('#(https://video-previews[a-z-_/\.0-9]+)|((?<=src=")https://elements-cover-images[a-zA-Z-_/\.0-9\?%&;=]+)#',
                        preg_replace('#amp;#', '', $this->getLink($post)), $matches);

                    if (array_key_exists(0, $matches)) {
                        $this->MadelineProto->messages->sendMessage(
                            ['peer' => '-100' . $channel,
                                'message' => $matches[0],
                                'reply_to_msg_id' => $key]);
                    }
                }
            }

            $offset_id = end($messages_Messages['messages'])['id'];

            sleep(2);
        } while (false);
    }
}
