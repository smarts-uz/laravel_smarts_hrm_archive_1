<?php

namespace App\Services\Envato;

use danog\MadelineProto\API;

trait EnvatoService
{
    public $MadelineProto;
    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PUT'));
        $this->MadelineProto->start();
    }

    protected function getLink($postLink)
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

    public function getPostId($start, $end) {

        $channel_id = env('CHANNEL_ID');

        for ($i = $start; $i <= $end; $i++) {

            $item = $this->MadelineProto->channels->getMessages([
            "channel" => '-100' . $channel_id,
            "id" => [$i]])['messages'];
                if (array_key_exists('media', $item[0]) && array_key_exists('webpage', $item[0]['media'])) {

                    if(array_key_exists('url', $item[0]['media']['webpage'])) {
                        var_dump($item[0]['id']);
                        $this->getComments(
                            $channel_id, $item[0]['id'],
                            $item[0]['replies']['replies'],
                            $item[0]['media']['webpage']['url'],
                            $item[0]['message']);

                    } else {

                        $this->MadelineProto->messages->sendMessage([
                            'peer' => '-100' . env('REPORT_CHANNEL_ID'),
                            'message' => 'https://t.me/c/' . $channel_id .'/' . $item[0]['id'] . ' 404 not found']);
                    }}
            sleep(1);
        }
    }
}
