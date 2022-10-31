<?php

namespace App\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

class MTProtoService
{
    public $MadelineProto;

    public function __construct()
    {
        $settings = new Settings;
        $settings->setAppInfo((new AppInfo)->setApiHash('0cc5751f00631d78d4dc5618864102dd')->setApiId(15108824));

        $this->MadelineProto = new API('D:/Sessions/akbarshoh.8522/session.madeline', $settings);
        $this->MadelineProto->start();
    }

    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->MadelineProto->messages->getReplies(['peer' => -100 . $split[4], 'msg_id' => $split[5]]);

        return $messages['messages'];
    }

    public function getFiles($comments)
    {
        $files = [];
        foreach ($comments as $message) {
            if (array_key_exists('media', $message)) {
                array_push($files, $message['media']['document']['attributes'][0]['file_name']);
            }
        }
        return $files;
    }

    public function getReplyMessage($url)
    {
        $url = explode('/', $url);
        $messages = $this->MadelineProto->messages->getHistory(['peer' => '-100' . $url[count($url) - 2], 'offset_id' => (int)end($url) + 1]);
        return $messages['messages'][0]['message'];

    }

    public function sync($path)
    {
        $file_system = new FileSystemService();
        $folders = scandir($path);
        $url_file = $file_system->searchForUrl($path);
        $url = $file_system->readUrl($url_file);
        $message = $this->getReplyMessage($url);

        $message_id = '';

        $comments = $this->getComments($url);
        $tg_files = $this->getFiles($comments);
        $storage_files = $file_system->getFIles($path);
        $to_tg = array_diff($storage_files, $tg_files);
        dd($comments);
        $nutgram = new NutgramService();
        foreach ($to_tg as $item) {
            $nutgram->sendDocument($path . '/' . $item, -1001732713545, 2078);
        }
    }
}
