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
                        if (!is_dir($path . 'files')) {
                            mkdir($path . 'files');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/files/');
                        break;
                    case 'messageMediaPhoto':
                        if (!is_dir($path . 'photos')) {
                            mkdir($path . 'photos');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/photos/');

                        break;
                    case'messageMediaVideo':
                        if (!is_dir($path . 'videos_files')) {
                            mkdir($path . 'videos_files');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/videos_files/');
                        break;
                    case'messageMediaAudio':
                        if (!is_dir($path . 'voice_messages')) {
                            mkdir($path . 'voice_messages');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/videos_messages/');
                        break;
                    case 'documentAttributeVideo':
                        if (!is_dir($path . 'video_files')) {
                            mkdir($path . 'video_files');
                        }
                        $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/video_files/');
                }


                if (array_key_exists('document', $messa['media'])) {
                    $this->MTProto->MadelineProto->downloadToDir($messa, $path . '/files/');
                    foreach ($messa['media']['document']['attributes'] as $attribute) {
                        if ($attribute['_'] == 'documentAttributeFilename') {
                            print_r(PHP_EOL);
                            print_r('Downloading ' . $attribute['file_name']);
                            print_r(PHP_EOL);
                        }
                    }
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
        if (!is_dir($path . '/files')) {
            mkdir($path . '/files');
        }
        file_put_contents($path . 'result.json', json_encode($update));
//                    $telegram = $export->FormatJson($update);
//                    file_put_contents($path . 'telegram.json', json_encode($telegram));
        if (!is_dir($path . 'files')) {
            mkdir($path . 'files');
        }
        $this->downloadMedia($update, $path);
        fopen("end.txt", "w");
    }

    public function FormatJson($messages)
    {
        $MTProto = new MTProtoService();
        $update = [];
        $chat = $MTProto->MadelineProto->getPwrChat(1244414566);
        $update['name'] = $chat['first_name'];
        if (array_key_exists('last_name', $chat)) {
            $update['last_name'] = $chat['last_name'];
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
                    $mess['forwarded_from'] = $message['fwd_from']['from_id']['user_id'];
                }
            }
            if (array_key_exists('edit_date', $message)) {
                $mess['edited'] = date("Y-n-j", $message['edit_date']) . 'T' . date("H:i:s", $message['edit_date']);
                $mess['edited_unixtime'] = (string)$message['edit_date'];
            }
            $chat = $this->MTProto->MadelineProto->getPwrChat($message['peer_id']['user_id']);
            $mess['from'] = $chat['first_name'];
            $mess['from_id'] = $chat['type'] . $message['peer_id']['user_id'];
            if (array_key_exists('reply_to', $message)) {
                $mess['reply_to_message_id'] = $message['reply_to']['reply_to_msg_id'];
            }
            $mess['text'] = array_key_exists('message', $message) ? $message['message'] : '';
            $update['messages'][] = $mess;
        }
        return $update;
    }

}
