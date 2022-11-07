<?php

namespace App\Services\MadelineProto;

class ExportService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function getMessages($id, $start, $end = null)
    {
        $messages = $this->MTProto->MadelineProto->messages->getHistory(['peer' => $id, 'limit' => 100]);
        $update = [];
        if($end == null){$end = strtotime("now");}
        foreach ($messages['messages'] as $message) {
            if ($message['date'] >= $start && $message['date'] <= $end) {
                print_r($message['date']);
                print_r(PHP_EOL);
                array_push($update, $message);
            }
        }
        return $update;
    }
}
