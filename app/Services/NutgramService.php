<?php

namespace App\Services;

use App\Models\Camera;
use SergiX44\Nutgram\Nutgram;
use function PHPUnit\Framework\isEmpty;


class NutgramService
{

    private $nutgram;
    private $rootPath;

    public function __construct()
    {
        $this->nutgram = new Nutgram(env('BOT_TOKEN'), ['timeout' => 20]);
        if (env('RUN_ON') === 'local') {
            $this->rootPath = env('STORAGE_PATH_LOCAL');
        } else {
            $path = env('SHARED_FOLDER');
            $this->rootPath = strtr($path, '\\', '//');
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }


    public function getCameraList()
    {
        $cameras = Camera::all();
        if (env('RUN_ON') === null) {
            exec('net use "\\\192.168.100.100" /user:"' . env('SHARED_FOLDER_USER') . '" "' . env('SHARED_FOLDER_PASSWORD') . '" /persistent:no');
        }
        return $cameras;
    }

    public function getOfficeCameras($id)
    {
        $cameras = Camera::where('office_id', $id)->get();
        $user = 'share';
        $password = 'admin123456';
        if (env('RUN_ON') === null) {
            exec('net use "\\\192.168.100.100" /user:"' . env('SHARED_FOLDER_USER') . '" "' . env('SHARED_FOLDER_PASSWORD') . '" /persistent:no');
        }
        return $cameras;
    }

    public function getMessageId($text)
    {
        // = new Nutgram(env('BOT_TOKEN'), ['timeout' => 20]);
        $updates = $this->nutgram->getUpdates();
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->text === $text) {
                    return $test->message_id;
                }
            }
        }
        sleep(3);
    }


    public function getActualData($camera)
    {
        //$bot = new Nutgram(env('BOT_TOKEN'), ['timeout' => 20]);
        $camera_folder = scandir('\\\\' . $this->rootPath . '/' . $camera->title);
//        dd(is_numeric(array_search($camera->folder, $camera_folder)));
        for ($i = array_search($camera->folder, $camera_folder); $i < count($camera_folder) - 2; $i++) {
            $current_dir = scandir('\\\\' . $this->rootPath . '/' . $camera->title . '/' . $camera_folder[$i]);
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
                $path = 'Z:/xiaomi_camera_videos/' . $camera->title . '/' . $camera_folder[$i];
                $video = fopen($path . '/' . $current_dir[$o], 'r+');
                print_r(PHP_EOL);
                print_r($current_dir[$o]);


                $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
//                    dd($text);
                $message_id = $this->getMessageId($text);
                print_r(PHP_EOL);
                print_r('Forward Message ID: ');
                print_r($message_id);

                if ($message_id === null) {
                    $this->nutgram->sendMessage($text, ['chat_id' => env('CHANNEL_ID')]);
                    sleep(3);
                    $message_id = $this->getMessageId($text);

                    $this->nutgram->sendDocument($video,
                        ['chat_id' => env('GROUP_ID'),
                            'reply_to_message_id' => $message_id,
                            'caption' => $current_dir[$o]]
                    );
                    $target = Camera::where('title', $camera->title);
                    $target->update([
                        'folder' => $camera_folder[$i],
                        'video' => $current_dir[$o]
                    ]);
                    sleep(3);
                } else {
                    $this->nutgram->sendDocument($video, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $current_dir[$o]]);
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
}
