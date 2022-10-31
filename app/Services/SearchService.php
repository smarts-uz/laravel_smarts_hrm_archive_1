<?php


namespace App\Services;


class SearchService
{

    public MTProtoService $MTProto;

    public function findMessage($channel_id, $message){
        $search = $this->MTProto->messages->search([
            "peer" => $channel_id,
            "q" => $message
        ]);

        if ($search["count"] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
