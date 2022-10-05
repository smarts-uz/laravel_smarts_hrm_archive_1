<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

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

    public function syncTelegramWanted($path)
    {
        $nutgram = new NutgramService();
        $url = $this->searchForUrl($path);
        if ($url != NULL) {
            $url = $this->readUrl($url);
            $post = $nutgram->getChannelPost($url);
            $comments = $nutgram->getComments($post);
            $files = $nutgram->getDocuments($comments);
        }
        $files_local = array_filter(array_slice(scandir($path),2), function ($item) use ($path) {
            if (strripos($item, '.') != 0) {
                return $item;
            }
        });
        return array_diff($files_local, $files);
    }

    public function syncStorageWanted($path)
    {
        $nutgram = new NutgramService();
        $url = $this->searchForUrl($path);
        if ($url != NULL) {
            $url = $this->readUrl($url);
            $post = $nutgram->getChannelPost($url);
            $comments = $nutgram->getComments($post);
            $files = $nutgram->getDocuments($comments);
        }
        $files_local = array_filter(array_slice(scandir($path),2), function ($item) use ($path) {
            if (strripos($item, '.') != 0) {
                return $item;
            }
        });
        return array_diff($files, $files_local);
    }

    public function sendToTelegram($path, $array, $reply){
        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 120]);
        foreach ($array as $item){
            $file = fopen($path . '/' . $item, 'r+');
            $bot->sendDocument($file, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $reply, 'caption' => $item]);
        }
    }

//    public function saveToSotrage($path, $messages, $array){
//        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
//        foreach ($array as $item){
//            $bot->downloadFile();
//            $bot->sendDocument($path . $item, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $reply, 'caption' => $item]);
//        }
//    }

}
