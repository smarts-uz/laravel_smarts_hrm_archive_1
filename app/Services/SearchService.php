<?php


namespace App\Services;


class SearchService
{

    public MTProtoService $MTProto;

    public function findMessage($channel_id, $message){
        $search = $this->MTProto->messages->search([
            "peer" => -1001563939142,
            "q" => '#php'
        ]);
        file_put_contents('test.txt', json_encode($search), FILE_APPEND);
    }

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
