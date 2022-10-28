<?php


namespace App\Services;


class SearchService
{

    public MTProtoService $MTProto;

    public function findMessage($channel_id, $message){
        $history = $this->MTProto->messages->getHistory(["peer" => $channel_id,]);
        $info = $this->MTProto->getInfo(-1001563939142);
        $Finfo = $this->MTProto->messages->getHistory(["peer" => -1001563939142,]);
        $messages = $Finfo["messages"];
        $Finfo = $this->MTProto->messages->getHistory(["peer" => -1001563939142, "offset_id" => 0, "limit" => $messages[0]["id"]]);
        $messages = $Finfo["messages"];
        echo $info["Chat"]['title']."\n";
        dd($messages);
    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
