<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class FileSystemService
{
    public $path;

    public function __construct()
    {
        exec('net use smb: \\' . env('SHARED_FOLDER') . '/user:' . env('SHARED_FOLDER_USER') . ' ' . env('SHARED_FOLDER_PASSWORD') . ' /persistent:Yes');
        $this->path = '/Volumes/Records/xiaomi_camera_videos/';
    }

    public function createUrl($path, $message_id, $channel_id)
    {
        $url = fopen($path . '/ALL.url', 'w');
        $text = view('components.url-file', compact('message_id', 'channel_id'));
        fwrite($url, $text);
        fclose($url);
    }

    public function createUrlFile($path, $urll)
    {
        $url = fopen($path . '/ALL.url', 'w');
        $text = view('components.urlfile', compact('urll'));
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

    public function scanCurFolder($path)
    {
        $list = array_diff(scandir($path), array('..', '.'));
        foreach ($list as $value) {
            if ($value != '..' && $value != '.') {
                if (is_dir($path . '/' . $value)) {
                    $list[$path . '/' . $value] = $this->scanFolder($path . '/' . $value);
                }
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
            if ($item != 'ALL.txt' && is_file($path . '/' . $item)) {
                $result = 1;
            }
        }
        return $result;
    }

    public function createPost($path, $txt_data, $titles){
        $python_service = new PythonService();
        $titles = [];

        $folders = scandir($path);
        foreach ($folders as $folder) {
            if(is_file($path . '/' . $folder)){
                $post_url = $python_service->searchForMessageMac($txt_data, $titles);
                $this->createUrlFile($path, $post_url);
                break;

            }
        }
    }

}
