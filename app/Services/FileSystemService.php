<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class FileSystemService
{
    private $path;

    public function __construct()
    {
       if (getenv('COMPUTERNAME') !== 'WORKPC') {
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

    public function createUrlFile($path, $urll)
    {

        $url = fopen($path . '/ALL.url', 'w');
        $text = "[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,11
[InternetShortcut]
IDList=
URL=" . $urll . "
IconIndex=13
HotKey=0
IconFile=C:\Windows\System32\SHELL32.dll";
        fwrite($url, $text);
        fclose($url);
    }

    public function scanFolder($folder)
    {
        $list = array_diff(scandir($folder), array('..', '.'));
        foreach ($list as $value) {
            if (is_dir($folder . '/' . $value)) {
                $list[$folder . '/' . $value] = $this->scanFolder($folder . '/' . $value);
            }
        }
        return $list;
    }

    public function searchForTxt($path)
    {
        $result = null;
        $list = scandir($path);
        foreach ($list as $item) {
            if (is_file($path . '/' . $item) && $item === 'ALL.txt') {
                $result = $path . '/' . $item;
            }
        }
        return $result;
    }

    public function readTxt($file)
    {
        $f = fopen($file, 'r+');
        $text = fread($f, filesize($file));
        $split = explode("\r\n", $text);
        return $split;
    }

    public function fileExists($path)
    {
        $result = 0;
        $list = scandir($path);
        foreach ($list as $item) {
            if ($item != 'ALL.txt' && is_file($path . '/' . $item) == 1 && !str_starts_with($item, '.')) {
                $result = is_file($path . '/' . $item);
            }
        }
        return $result;
    }

    public function createPost($path, $txt_data, $titles = [])
    {
        $python_service = new PythonService();
        $dir = scandir($path);
        foreach ($dir as $item) {
            if (is_file($path . '/' . $item) && $item != 'ALL.txt' && $item != 'ALL.url') {
                $post_url = $python_service->searchForMessage($txt_data, $titles);
                $this->createUrlFile($path, $post_url);
                break;
            } else if (is_dir($path . '/' . $item) && $item != '- Theory' && !str_starts_with($item, '@') && !str_starts_with($item, '.')) {
                array_push($titles, $item);
                $this->createPost($path . '/' . $item, $txt_data, $titles);
            } else if ($item == '- Theory' && $this->fileExists($path . '/- Theory')) {
//                array_push($titles, $item);
                $this->createPost($path, $txt_data, $titles);
            }
        }
    }

    public function syncSubFolder($path, $txt_data, $titles)
    {
        $folders = scandir($path);
        foreach ($folders as $folder) {
            if (is_dir($path . '/' . $folder) && $folder != '- Theory' && !str_starts_with($folder, '@') && !str_starts_with($folder, '.')) {
                array_push($titles, $folder);
                $this->createPost($path . '/' . $folder, $txt_data, $titles);
                $this->syncSubFolder($path . '/' . $folder, $txt_data, $titles);
            }
        }
    }
}
