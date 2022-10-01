<?php

namespace App\Services;

use App\Models\Camera;
use SergiX44\Nutgram\Nutgram;

class NutgramService
{
    public function getCameraList()
    {
        $cameras = Camera::all();
        return $cameras;
    }

    public function getMessageId($text)
    {
        $bot = new Nutgram("5743173293:AAF33GAKELp-Id9y00EhIJRrpWI37umZ788");
        $updates = $bot->getUpdates();
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


    public function getActualData($camera)
    {
        $camera_folder = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos/' . $camera->title);
        for ($i = array_search($camera->folder, $camera_folder); $i < count($camera_folder) - 2; $i++) {
            $current_dir = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos/' . $camera->title . '/' . $camera_folder[$i]);
            print_r('Folder: ' . $camera_folder[$i]);
            print_r(PHP_EOL);
            print_r('Files: ');
            print_r(PHP_EOL);
            if ($i == array_search($camera->folder, $camera_folder)) {
                for ($o = array_search($camera->video, $current_dir) + 2; $o <= count($current_dir) - 2; $o++) {
                    $path = 'Z:/xiaomi_camera_videos/' . $camera->title . '/' . $camera_folder[$i];
                    $video = fopen($path . '/' . $current_dir[$o], 'r+');
                    print_r($current_dir[$o]);

                    $bot = new Nutgram("5743173293:AAF33GAKELp-Id9y00EhIJRrpWI37umZ788");
                    $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
//                    dd($text);
                    $message_id = $this->getMessageId($text);
                    var_dump($message_id);
                    if ($message_id == null) {
                        $bot->sendMessage($text, ['chat_id' => '-1001680227740']);
                        sleep(3);
                        $message_id = $this->getMessageId($text);
                        var_dump($message_id);
                        $bot->sendDocument($video, ['chat_id' => '-1001626673572', 'reply_to_message_id' => $message_id]);
                        sleep(3);
                    } else {
                        var_dump($message_id);
                        $bot->sendDocument($video, ['chat_id' => '-1001626673572', 'reply_to_message_id' => $message_id]);
                        sleep(3);
                    }
                }
            }
        }
    }
}
