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

    public function sendFile($camera, $video, $message_id, $caption, $folder)
    {
        $this->bot->sendDocument($video, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $caption]);
        $target = Camera::where('title', $camera->title);
        $target->update(['folder' => $folder, 'video' => $caption]);
        sleep(3);
    }

    public function getActualData($camera)
    {
        $camera_folder = scandir($this->file_system->path . $camera->title);
        for ($i = array_search($camera->folder, $camera_folder); $i < count($camera_folder) - 2; $i++) {
            $current_dir = scandir($this->file_system->path . $camera->title . '/' . $camera_folder[$i]);
            $q = (is_numeric(array_search($camera->video, $current_dir))) ? array_search($camera->video, $current_dir) : 1;
            for ($o = $q + 1; $o <= count($current_dir) - 1; $o++) {
                $path = $this->file_system->path . $camera->title . '/' . $camera_folder[$i];
                $video = fopen($path . '/' . $current_dir[$o], 'r+');
                $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
                $message_id = $this->getMessageId($text);
                if ($message_id == null) {
                    $this->bot->sendMessage($text, ['chat_id' => env('CHANNEL_ID')]);
                    sleep(3);
                    $message_id = $this->getMessageId($text);
                }
                $this->sendFile($camera, $video, $message_id, $current_dir[$o], $camera_folder[$i]);
            }
        }
    }

}
