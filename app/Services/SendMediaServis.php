<?php


namespace App\Services;


class SendMediaServis
{
    public $MTProto;

    public function sendMedia($url, $chat_id, $descr, $reply_to = null){

        $this->MTProto->MadelineProto->messages->sendMedia(["peer" => $chat_id, "media" => ['_' => 'inputMediaUploadedDocument','file' => $url], "message" => $descr, "reply_to_msg_id"=>$reply_to]);

    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
