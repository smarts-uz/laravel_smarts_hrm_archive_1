<?php

namespace App\Services\MadelineProto;

use danog\MadelineProto\messages;
use danog\MadelineProto\MTProto;

class ExportService
{
    public MTProtoService $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function getMessages($id, $start, $end): array
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
        foreach ($messages as $messa) {
            if (array_key_exists('media', $messa)) {
                switch ($messa['media']['_']) {
                    case 'messageMediaDocument':
                        foreach ($messa['media']['document']['attributes'] as $attribute) {
                            if ($attribute['_'] == 'documentAttributeVideo') {
                                if ($attribute['round_message'] == true) {
                                    if (!is_dir($path . 'rounded_video_messages')) {
                                        mkdir($path . 'rounded_video_messages');
                                    }
                                    $this->MTProto->MadelineProto->downloadToDir($messa['media'], $path . 'rounded_video_messages/');
                                } else {
                                    if (!is_dir($path . 'videos_files')) {
                                        mkdir($path . 'videos_files');
                                    }
                                    $this->MTProto->MadelineProto->downloadToDir($messa['media'], $path . 'videos_files/');
                                }
                                break;
                            }else if ($attribute['_'] == 'documentAttributeAudio') {
                                if (!is_dir($path . 'voice_messages')) {
                                    mkdir($path . 'voice_messages');
                                }
                                $this->MTProto->MadelineProto->downloadToDir($messa['media'], $path . 'voice_messages/');
                                break;
                            }else if($attribute['_'] == 'documentAttributeFilename'){
                                if (!is_dir($path . 'files')) {
                                    mkdir($path . 'files');
                                }
                                $this->MTProto->MadelineProto->downloadToDir($messa['media'], $path . 'files/');
                            }
                        }
                        break;
                    case 'messageMediaPhoto':
                        if (!is_dir($path . 'photos')) {
                            mkdir($path . 'photos');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa['media'], $path . 'photos/');
                        break;
                }
            }
        }
    }

    public function folderPath($id, $path, $date)
    {

        $chat = $this->MTProto->MadelineProto->getPwrChat($id);
        $title = '';
        if ($chat['type'] == 'supergroup' || $chat['type'] == 'channel') {
            $title = $chat['title'];
        } else if ($chat['type'] == 'user') {
            $title = $chat['first_name'];
        }
        //Title
        if (!is_dir($path . $title)) {
            mkdir($path . $title);
        }
        $path .= $title . '/';

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

    public function export($channel_id, $unix_start, $end, $date)
    {
        $path = $this->folderPath($channel_id, setting('file-system.tg_export'), $date);
        if (is_file($path . 'end.txt')) {
            return;
        }
        $update = $this->getMessages($channel_id, $unix_start, $end);
        file_put_contents($path . 'result.json', json_encode($update));
        $telegram = $this->FormatJson($channel_id, $update);
        file_put_contents($path . 'telegram.json', json_encode($telegram));
        $this->downloadMedia($update, $path);
        fopen($path . "end.txt", "w");
    }

    public function FormatJson($id,$messages)
    {
        $update = [];
        $chat = $this->MTProto->MadelineProto->getPwrChat($id);

        if($chat['type'] == 'user'){
            $mess['name'] = $chat['first_name'];
        }else{
            $mess['name'] = $chat['title'];
        }

        $update['type'] = $chat['type'];
        $update['id'] = $chat['id'];
        $update['messages'] = [];

        for ($i = count($messages) - 1; $i > -1; $i--) {
            $message = $messages[$i];
            $mess = [];
            $mess['id'] = $message['id'];
            $mess['type'] = $message['_'];
            $mess['date'] = date("Y-n-j", $message['date']) . 'T' . date("H:i:s", $message['date']);
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
                if ($message['media']['_'] == 'messageMediaPhoto') {
                    $mess['photo'] = 'Photo';
                }
            }
            if (array_key_exists('fwd_from', $message)) {
                if (array_key_exists('from_id', $message['fwd_from'])) {
                    $mess['forwarded_from'] = $message['fwd_from']['from_id'][array_key_exists('user_id', $message['fwd_from']['from_id']) ? 'user_id' : 'channel_id'];
                }
            }
            if (array_key_exists('edit_date', $message)) {
                $mess['edited'] = date("Y-n-j", $message['edit_date']) . 'T' . date("H:i:s", $message['edit_date']);
                $mess['edited_unixtime'] = (string)$message['edit_date'];
            }
            if (array_key_exists('from_id', $message)) {
                if(array_key_exists('user_id', $message['from_id'])){
                    $chat = $this->MTProto->MadelineProto->getPwrChat($message['from_id']['user_id']);
                    $mess['from'] = $chat['first_name'];
                    $mess['from_id'] = $chat['type'] . $message['from_id']['user_id'];
                }else{
                    $chat = $this->MTProto->MadelineProto->getPwrChat('-100' . $message['from_id']['channel_id']);
                    $mess['from'] = $chat['title'];
                    $mess['from_id'] = $chat['type'] . $message['from_id']['channel_id'];
                }
            } else {

                $chat = $this->MTProto->MadelineProto->getPwrChat(array_key_exists('channel_id', $message['peer_id']) ? '-100' . $message['peer_id']['channel_id'] : $message['peer_id']['user_id']);
                $mess['from'] = $chat[array_key_exists('title', $chat) ? 'title' : 'first_name'];
                $mess['from_id'] = $chat['type'] . $message['peer_id'][array_key_exists('channel_id', $message['peer_id']) ? 'channel_id' : 'user_id'];
            }
            if (array_key_exists('reply_to', $message)) {
                $mess['reply_to_message_id'] = $message['reply_to']['reply_to_msg_id'];
            }
            $mess['text'] = array_key_exists('message', $message) ? $message['message'] : '';
            $update['messages'][] = $mess;
        }
        return $update;
    }

}
