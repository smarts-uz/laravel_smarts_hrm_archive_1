<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace App\Services;

use App\Models\Camera;
use SergiX44\Nutgram\Nutgram;
use function PHPUnit\Framework\isEmpty;

class ProcessCameraService
{
    protected $path;
    protected $cameraId;
    protected Nutgram $tg_bot;

    public function __construct($id = null){
        $this->cameraId = $id;
        if (getenv('COMPUTERNAME') === 'WORKPC') {
            exec('net use Z: \\' . env('SHARED_FOLDER') . '/user:' . env('SHARED_FOLDER_USER') . ' ' . env('SHARED_FOLDER_PASSWORD') . ' /persistent:Yes');
            $this->path = 'Z:/';
        }
        else {
            $this->path = env('ROOT_PATH');
        }
        $this->tg_bot = new Nutgram(env('TELEGRAM_TOKEN'));
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }


    public function getVideoFilelist()
    {
        $filelist = [];
        $getCamerasFolderName = Camera::where('id', $this->cameraId)->get('title')->first();
        if (is_dir($this->path.'\\'.$getCamerasFolderName->title))
        {
            $folder_items = scandir($this->path.'\\'.$getCamerasFolderName->title);
                foreach ($folder_items as $folder_item) {
                    if ($folder_item != '.' && $folder_item != '..') {
                        $filelist[] = $folder_item;
                    }
                }
        }
        else {
            $nofolder_notify = 'There is no folder with such name';
            return $nofolder_notify;
        }
        return $filelist;
    }

    private function searchVideoInTGChanel(){
        $this->tg_bot->start();
        $this->tg_bot->onText(function($text) {
            if (isEmpty($text)) {
                return;
            }
            $this->tg_bot->say($text);
        });
    }
}
