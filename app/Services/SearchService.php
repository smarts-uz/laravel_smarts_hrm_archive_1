<?php


namespace App\Services;


class SearchService
{

    public MTProtoService $MTProto;

    public function findMessage($channel_id, $message){
        $history = $this->MTProto->messages->getHistory(["peer" => $channel_id,]);
    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
