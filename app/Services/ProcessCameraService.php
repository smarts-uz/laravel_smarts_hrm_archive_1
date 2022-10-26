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
use function PHPUnit\Framework\isEmpty;

class ProcessCameraService
{
    protected $path;
    protected $cameraId;

    public function __construct($id = null){
        $this->cameraId = $id;
        if (getenv('COMPUTERNAME') === 'WORKPC') {
            exec('net use Z: \\' . env('SHARED_FOLDER') . '/user:' . env('SHARED_FOLDER_USER') . ' ' . env('SHARED_FOLDER_PASSWORD') . ' /persistent:Yes');
            $this->path = 'Z:/';
        }
        else {
            $this->path = env('ROOT_PATH');
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function getVideoFilelist()
    {
        $getCamerasFolder = Camera::where('id', $this->cameraId)->get('title')->first();
        $filelist = [];
        if (is_dir($this->path.'\\'.$getCamerasFolder->title))
        {
            $folder_items = scandir($this->path.'\\'.$getCamerasFolder->title);
                foreach ($folder_items as $folder_item) {
                    if ($folder_item != '.' && $folder_item != '..') {
                        $filelist[] = $folder_item;
                    }
                }
        }
        else {
            $nofolder_notification = 'There is no folder with such name';
            return $nofolder_notification;
        }

/*       foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filelist[] = $file;
            }
        }*/
        return $filelist;
    }
}
