<?php

namespace App\Services\MadelineProto;

use danog\MadelineProto\messages;
use danog\MadelineProto\MTProto;

class ExportService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function getMessages($id, $start, $end)
    {
        $messages = $this->MTProto->MadelineProto->messages->getHistory(['peer' => $id, 'offset_date' => $end, 'limit' => 100]);
        $update = [];
        foreach ($messages['messages'] as $message) {
            if ($message['date'] > (int)$start) {
                array_push($update, $message);
            }
        }
        return $update;
    }

    public function downloadMedia($messages, $path)
    {
        print_r(111111);
        if (!is_dir($path . 'files')) {
            mkdir($path . 'files');
        }
        $path .= 'files/';
        foreach ($messages as $message) {
            if (array_key_exists('media', $message)) {
                try {
                    print_r('Downloading ' . $message['media']['document']['attributes'][0]['file_name']);
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                }
                yield $this->MTProto->MadelineProto->downloadToDir($message['media'], $path . '/');
            }
        }
    }

    public function folderPath($id, $path, $date)
    {

        //$chat = $this->MTProto->MadelineProto->channels->getFullChannel(['channel' => $id]);

        //Title
        if (!is_dir($path . $id)) {
            mkdir($path . $id);
        }
        $path .= $id . '/';

        //Year
        if (!is_dir($path . $date['year'])) {
            mkdir($path . $date['year']);
        }
        $path .= $date['year'] . '/';


        //month
        if (!is_dir($path . $date['month'])) {
            mkdir($path . $date['month']);
        }
        $path .= $date['month'] . '/';

        //day
        if (!is_dir($path . $date['day'])) {
            mkdir($path . $date['day']);
        }
        $path .= $date['day'] . '/';

        //Hours
        if ($date['hour'] != "") {
            if (!is_dir($path . $date['hour'])) {
                mkdir($path . $date['hour']);
            }
            $path .= $date['hour'] . '/';
        }
        return $path;
    }

    public function export()
    {
        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_start = strtotime($date_start);
        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        $path = $this->folderPath($channel_id, 'C:\Users\Pavilion\Documents\MadelineProto\JSONs\Updates\\', $date);
        if ($date['hour'] == "") {
            if ($unix_start + 86400 <= $unix_end) {
                $update = $this->getMessages($channel_id, $unix_start, $unix_start + 86400);
                file_put_contents($path . 'result.json', json_encode($update));
                $unix_start += 86400;
                print_r(gmdate("j.n.Y H:iP", $unix_start));
            }
        }
    }

    public function ForwardJson($messages)
    {
        $MTProto = new MTProtoService();
        $update = [];
        $chat = $MTProto->MadelineProto->getPwrChat(1244414566);
        $update['name'] = $chat['first_name'];
        if(array_key_exists('last_name', $chat)){
            $update['last_name'] = $chat['last_name'];
        }
        $update['type'] = $chat['type'];
        $update['id'] = $chat['id'];
        $update['messages'] = [];

        for($i = count($messages)-1; $i>-1; $i--) {
            $message = $messages[$i];
            $mess = [];
            $mess['id'] = $message['id'];
            $mess['type'] = $message['_'];
            $mess['date'] = date("j.n.Y H:iP", $message['date']);
            $mess['date_unixtime'] = (string)$message['date'];
            if (array_key_exists('media', $message)) {
                if (array_key_exists('document', $message['media'])) {
                    foreach ($message['media']['document']['attributes'] as $attribute) {
                        if ($attribute['_'] == 'documentAttributeFilename') {
                            $mess['file'] = 'files/' . $attribute['file_name'];
                        }
                        if ($attribute['_'] == 'documentAttributeAudio') {
                            $mess['media_type'] = 'voice_message';
                        }
                    }
                    $mess['mime_type'] = $message['media']['document']['mime_type'];
                }
                if($message['media']['_']  == 'messageMediaPhoto'){
                    $mess['photo'] = 'Photo';
                }
            }
            if(array_key_exists('fwd_from',$message)){
                if(array_key_exists('from_id', $message['fwd_from'])){
                    $mess['forwarded_from'] = $message['fwd_from']['from_id']['user_id'];
                }
            }
            if(array_key_exists('edit_date', $message)){
                $mess['edited'] = date("j.n.Y H:iP", $message['edit_date']);
                $mess['edited_unixtime'] = (string)$message['edit_date'];
            }
            if(array_key_exists('reply_to', $message)){
                $mess['reply_to_message_id'] = $message['reply_to']['reply_to_msg_id'];
            }
            $mess['text'] = array_key_exists('message', $message) ? $message['message'] : '';
            array_push($update['messages'], $mess);
        }
        return $update;
    }

}
