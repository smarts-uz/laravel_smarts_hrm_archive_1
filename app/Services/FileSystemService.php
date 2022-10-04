<?php

namespace App\Services;

class FileSystemService
{
    public $path;

    public function __construct()
    {
        exec('net use Z: \\' . env('SHARED_FOLDER') . '/user:' . env('SHARED_FOLDER_USER') . ' ' . env('SHARED_FOLDER_PASSWORD') . ' /persistent:Yes');
        $this->path = 'Z:/';
    }

    public function createUrl($path)
    {
        $url = fopen($path . '/ALL.url', 'w');
        $text = '[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,11
[InternetShortcut]
IDList=
URL=https://github.com/search?l=PHP&o=desc&q=telegram&s=stars&type=Repositories
IconIndex=13
HotKey=0
IconFile=C:\Windows\System32\SHELL32.dll';
        fwrite($url, $text);
        fclose($url);
    }

    public function readUrl($path)
    {
        $file = fopen($path, 'r+');
        $text = fread($file, filesize($path));
        $split = explode("\n", $text);
        $result = preg_grep("/^url=/i", $split);
        return substr($result[4], 4);
    }

    public function searchForUrl($path)
    {
        $files = scandir($path);
        $url = NULL;
        foreach ($files as $file) {
            if (substr($file, -4) === '.url') {
                $url = $path . '/' . $file;
            }
        }
        return $url;
    }

}
