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

        $this->MadelineProto = new API(env('SESSION_PUT'), $settings);
        $this->MadelineProto->start();
    }

    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->MadelineProto->messages->getReplies(['peer' => '-100' . $split[4], 'msg_id' => $split[5]]);

        return $messages['messages'];
    }

    public function getFiles($comments)
    {
        $files = [];
        foreach ($comments as $message) {
            if (array_key_exists('media', $message)) {
                foreach ($message['media']['document']['attributes'] as $item){
                    if($item['_'] == 'documentAttributeFilename'){
                        array_push($files, $item['file_name']);
                    }
                }
            }
        }
        return $files;
    }

    public function sync($path)
    {
        $file_system = new FileSystemService();
        $MTProto = new MTProtoService();

        $url_file = $file_system->searchForUrl($path);
        $url = $file_system->readUrl($url_file);
        $split = explode("/", $url);
        $message = $MTProto->MadelineProto->messages->getDiscussionMessage(['peer' => '-100'  . $split[4], 'msg_id' => (int)$split[5]]);
        $comments = $this->getComments($url);

        $tg_files = $this->getFiles($comments);
        $storage_files = $file_system->getFIles($path);

        echo '<pre>';
        print_r($tg_files);
        //print_r($storage_files);
    }
}
