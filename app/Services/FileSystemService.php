<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class FileSystemService
{
    public $path;

    public function __construct()
    {
//        exec('net use Z: \\' . env('SHARED_FOLDER') . '/user:' . env('SHARED_FOLDER_USER') . ' ' . env('SHARED_FOLDER_PASSWORD') . ' /persistent:Yes');
        $this->path = 'Z:/';
    }

    public function createUrl($path, $message_id, $channel_id)
    {
        $url = fopen($path . '/ALL.url', 'w');
        $text = view('components.url-file', compact('message_id', 'channel_id'));
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

    public function TelegramWanted($path)
    {
        $nutgram = new NutgramService();
        $post = $nutgram->getChannelPost($path);
        $comments = $nutgram->getComments($post);
        $files = $nutgram->getDocuments($comments);
        $files_local = array_filter(array_slice(scandir($path), 2), function ($item) use ($path) {
            if (!is_dir($path . '/' . $item)) {
                return $item;
            }
        });
        return array_diff($files_local, $files);
    }

    public function StorageWanted($path)
    {
        $nutgram = new NutgramService();
        $url = $this->searchForUrl($path);
        if ($url != NULL) {
            $url = $this->readUrl($url);
            $post = $nutgram->getChannelPost($url);
            $comments = $nutgram->getComments($post);
            $files = $nutgram->getDocuments($comments);
        }
        $files_local = array_filter(array_slice(scandir($path), 2), function ($item) use ($path) {
            if (strripos($item, '.') != 0) {
                return $item;
            }
        });
        return array_diff($files, $files_local);
    }


//    public function saveToSotrage($path, $messages, $array){
//        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
//        foreach ($array as $item){
//            $bot->downloadFile();
//            $bot->sendDocument($path . $item, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $reply, 'caption' => $item]);
//        }
//    }


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

}
