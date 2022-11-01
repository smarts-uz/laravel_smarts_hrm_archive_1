<?php


namespace App\Services;


class SendMediaServis
{
    public $MTProto;

    public function sendMedia($url, $chat_id, $descr){

        $this->MTProto->MadelineProto->messages->sendMedia(["peer" => $chat_id, "media" => ['_' => 'inputMediaUploadedDocument','file' => $url], "message" => $descr]);

    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
