<?php

namespace App\Services;

use App\Models\Camera;
use SergiX44\Nutgram\Nutgram;


class NutgramService
{
    protected $bot;
    protected $file_system;

    public function __construct()
    {
        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
        $this->bot = $bot;
        $this->file_system = new FileSystemService();
    }

    public function getCameraList()
    {
        $cameras = Camera::all();
        return $cameras;
    }

    public function getOfficeCameras($id)
    {
        $cameras = Camera::where('office_id', $id)->get();
        return $cameras;
    }

    public function getMessageId($text)
    {
        $updates = $this->bot->getUpdates();
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->text == $text) {
                    return $test->message_id;
                }
            }
        }
        sleep(3);
    }

    public function getChannelPost($url)
    {
        $url_arr = explode("/", $url);

        $updates = $this->bot->getUpdates();
        foreach ($updates as $update) {
            if ($update->channel_post) {
                $test = $update->channel_post;
                $qwe = $test->chat;
                if ($qwe->id == '-100' . $url_arr[count($url_arr) - 2] && $test->message_id == $url_arr[count($url_arr) - 1]) {
                    return $test;
                }
            }
        }

    }

    public function getMessagesId($message)
    {
        $updates = $this->bot->getUpdates();
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->text == $message->text && $test->chat->id == env("GROUP_ID")) {
                    return $test;
                }
            }
        }
        sleep(3);
    }

    public function getComments($channel_post)
    {
        $messages = array();
        $message = $this->getMessagesId($channel_post);
        $updates = $this->bot->getUpdates();
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->reply_to_message) {
                    $reply_to = $test->reply_to_message;
                    if ($reply_to->message_id === $message->message_id) {
                        array_push($messages, $test);
                    }
                }
            }
        }
        return $messages;
    }

    public function getMessageReply($ids)
    {
        $updates = $this->bot->getUpdates();
        $chat = NULL;
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->chat->id === $ids[0]) {
                    $chat = $test->chat->id;
                }
            }
        }
    }

    public function getActualData($camera)
    {
        $camera_folder = scandir($this->file_system->path . $camera->title);
        for ($i = array_search($camera->folder, $camera_folder); $i < count($camera_folder) - 2; $i++) {
            $current_dir = scandir($this->file_system->path . $camera->title . '/' . $camera_folder[$i]);
            print_r('Folder: ' . $camera_folder[$i]);
            print_r(PHP_EOL);
            print_r('Files: ');
            print_r(PHP_EOL);
            global $q;
            if (is_numeric(array_search($camera->video, $current_dir))) {
                $q = array_search($camera->video, $current_dir);
            } else {
                $q = 1;
            }
            for ($o = $q + 1; $o <= count($current_dir) - 1; $o++) {
                $path = $this->file_system->path . $camera->title . '/' . $camera_folder[$i];
                $video = fopen($path . '/' . $current_dir[$o], 'r+');
                print_r(PHP_EOL);
                print_r($current_dir[$o]);
                $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
                $message_id = $this->getMessageId($text);
                print_r(PHP_EOL);
                print_r('Forward Message ID: ');
                print_r($message_id);
                if ($message_id == null) {
                    $this->bot->sendMessage($text, ['chat_id' => env('CHANNEL_ID')]);
                    sleep(3);
                    $message_id = $this->getMessageId($text);
                    $this->bot->sendDocument($video, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $current_dir[$o]]);
                    $target = Camera::where('title', $camera->title);
                    $target->update([
                        'folder' => $camera_folder[$i],
                        'video' => $current_dir[$o]
                    ]);
                    sleep(3);
                } else {
                    $this->bot->sendDocument($video, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $current_dir[$o]]);
                    $target = Camera::where('title', $camera->title);
                    $target->update([
                        'folder' => $camera_folder[$i],
                        'video' => $current_dir[$o]
                    ]);
                    sleep(3);
                }
            }
        }
    }

    public function getDocuments($messages)
    {
        $files = array();
        foreach ($messages as $message){
            if($message->document){
                $file = $message->document;
                array_push($files, $file->file_name);
            }
        }
        return $files;
    }

}
