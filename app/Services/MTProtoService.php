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

    public function sync($path)
    {
        $file_system = new FileSystemService();
        $MTProto = new MTProtoService();

        $url_file = $file_system->searchForUrl($path);
        $url = $file_system->readUrl($url_file);
        //dd($url);
        $split = explode("/", $url);
        $message = $MTProto->MadelineProto->messages->getDiscussionMessage(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => (int)$split[count($split) - 1]]);
        $comments = $this->getComments($url);
        $tg_files = $this->getFiles($comments);
        $storage_files = $file_system->getFIles($path);
        $to_tg = array_diff($storage_files, $tg_files);
        $to_st = array_diff($tg_files, $storage_files);
        foreach ($to_tg as $item) {
            $descr = $file_system->caption($path . '/' . $item);
            print_r($path . '/' . $item);
            print_r($descr);
            $MTProto->MadelineProto->messages->sendMedia(["peer" => '-100' . $message['messages'][0]['peer_id']['channel_id'],
                "reply_to_msg_id"=>(int)$message['messages'][0]['id'], "media" => ['_' => 'inputMediaUploadedDocument',
                    'file' => $path . '/' . $item, 'attributes' => [
                        ['_' => 'documentAttributeFilename', 'file_name' => $item]
                    ]], "message" => $descr]);
        }

        foreach ($to_st as $item){
            $MTProto->downloadMedia($comments, $item, $path);
        }
    }
}
