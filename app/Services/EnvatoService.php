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

    public function run($channel, $date)
    {
       /* $messages_coments = $this->MadelineProto->messages->getHistory([
            'peer' => $comment,
            'limit' => 9999,
            'max_id' => 999999,]);

        $comments = [];
        foreach ($messages_coments['messages'] as $key => $item) {
            if (array_key_exists('replies',$item)) {
                $comments[$item['replies']['max_id']][] = $item['message'];
            }
        }*/
        $offset_id = 0;
        $limit = 100;
        $posts = [];
        $comments = [];
        do {
            $messages_Messages = $this->MadelineProto->messages->getHistory([
            'peer' => '-100'. $channel,
            'offset_date' => $date,
            'offset_id' => $offset_id,
            'limit' => $limit,
            'max_id' => 99999
            ]);

            if (count($messages_Messages['messages']) == 0) break;

            foreach ($messages_Messages['messages'] as $key => $item) {
                if (array_key_exists('media',$item) and array_key_exists('replies',$item)) {
                    if (array_key_exists('webpage',$item['media'])) {
                        $posts[$item['id']] = $item['media']['webpage']['url'];
                    }
                }
                if (array_key_exists('media',$item) and array_key_exists('reply_to',$item)) {
                    $comments[$item['reply_to']['reply_to_msg_id']][] = $item['message'];
                }
            }
            foreach ($posts as $key => $post) {
                if(array_key_exists($key,$comments)) {
                    foreach($comments[$key] as $comment) {
                        preg_match('/#previews#/i', $comment, $match);
                        var_dump($match);
                        if (!is_array($match)) {
                            // 1. инициализация
                            $ch = curl_init();
                            // 2. указываем параметры, включая url
                            curl_setopt($ch, CURLOPT_URL, $post);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)');
                            // 3. получаем HTML в качестве результата
                            $subject = curl_exec($ch);
                            preg_match('#https://video-previews[a-z-_/\.0-9]+#', $subject, $matches);
                            //preg_match('#https#', $subject, $matches);
                            // 4. закрываем соединение
                            curl_close($ch);
                            if (array_key_exists(0,$matches)) {
                                var_dump($matches[0]);
                                file_put_contents($key . 'a.html', $matches[0]);
                                $this->MadelineProto->messages->sendMessage(
                                  ['peer' => '-100' . $channel,
                                  'message' => $matches[0],
                                  'reply_to_msg_id' => $key]);
                            }
                        }
                    }
                } else {
                    // 1. инициализация
                    $ch = curl_init();
                    // 2. указываем параметры, включая url
                    curl_setopt($ch, CURLOPT_URL, $post);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)');
                    // 3. получаем HTML в качестве результата
                    $subject = curl_exec($ch);
                    preg_match('#https://video-previews[a-z-_/\.0-9]+#', $subject, $matches);
                    //preg_match('#https#', $subject, $matches);
                    // 4. закрываем соединение
                    curl_close($ch);
                    if (array_key_exists(0,$matches)) {
                        //var_dump($matches[0]);
                        file_put_contents($key . 'a.html', $matches[0]);
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

        file_put_contents('a.json', json_encode($posts), );
        file_put_contents('d.json', json_encode($comments), );
    }
}
