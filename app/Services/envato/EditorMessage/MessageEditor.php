<?php


namespace App\Services\Envato\MessageEditor;


use App\Services\MTProtoService;

class EditorService
{
    public $MProto;

    public function editMessage($chat_id, $message_id, $message){
        $this->MProto->MadelineProto->messages->editMessage(["peer" => $chat_id, "id" => $message_id, "message" => $message]);
    
    }



    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }
}
