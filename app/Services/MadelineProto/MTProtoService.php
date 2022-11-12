<?php

namespace App\Services\MadelineProto;

use App\Services\TaskStatus\HandleStatusService;
use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

class MTProtoService
{
    public $MadelineProto;
    public $settings;

    public function __construct()
    {
        $this->settings = new Settings;
        $this->settings->setAppInfo((new AppInfo)->setApiHash('adcaaf6ff60778f454ee90f3a6c26c7b')->setApiId(9330195));
        $this->MadelineProto = new API(env('SESSION_PUT') . '/session.madeline', $this->settings);
        $this->MadelineProto->start();
        $this->MadelineProto->messages->sendMessage(["peer" => 1307688882, "message" => "test"]);
    }

    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->MadelineProto->messages->getReplies(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]]);

        return $messages['messages'];
    }

    public function getFiles($comments)
    {
        $files = [];
        foreach ($comments as $message) {
            if (array_key_exists('media', $message)) {
                foreach ($message['media']['document']['attributes'] as $item) {
                    if ($item['_'] == 'documentAttributeFilename') {
                        array_push($files, $item['file_name']);
                    }
                }
            }
        }
        return $files;
    }

    public function downloadMedia($comments, $file_name, $path)
    {
        foreach ($comments as $message) {
            if (array_key_exists('media', $message)) {
                foreach ($message['media']['document']['attributes'] as $item) {
                    if ($item['_'] == 'documentAttributeFilename') {
                        if (in_array($item['file_name'], $file_name)) {
                            yield $this->MadelineProto->downloadToDir($item['media'], $path . '/');;

                        }
                    }
                }
            }
        }
    }

    public function sync()
    {

    }
}
