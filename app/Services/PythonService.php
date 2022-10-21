<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class PythonService

{
    public function searchForMessage($txt_data, $titles = [])
    {
        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
        $text = $this->folders($txt_data, $titles);
        print_r($text);
        print_r(PHP_EOL);
        $getUrl = exec(' D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Smart_Software\laravel_xiaomi_bot\Python\search.py "' . $txt_data[1] . '::' . $text . '"');
        if ($getUrl === 'Message not found' || $getUrl === "") {
            $bot->sendMessage($text, ['chat_id' => $txt_data[1]]);
            print_r($text);
            print_r(PHP_EOL);
            $getUrl = exec(' D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Smart_Software\laravel_xiaomi_bot\Python\search.py "' . $txt_data[1] . '::' . $text . '"');
        }
        return $getUrl;
    }

    public function folders($txt_data, $folders = [])
    {
        if (count($folders) != 0) {
            $text = '';
            foreach (array_reverse($folders) as $title) {
                $text .= $title;
                $text .= ' | ';
            }
            $text .= $txt_data[0];
            return $text;
        } else {
            return $txt_data[0];
        }

    }

    public function subFolderSync($path)
    {
        exec('C:\Users\mamad\PycharmProjects\telethon-scripts\venv\Scripts\python.exe D:\Smart_Software\laravel_xiaomi_bot\Python\main.py "' . $path . '"');
        $syncs = scandir($path);
        foreach ($syncs as $sync) {
            if (is_dir($path . '/' . $sync) && !str_starts_with($sync, '@') && !str_starts_with($sync, '.')) {
                exec('C:\Users\mamad\PycharmProjects\telethon-scripts\venv\Scripts\python.exe D:\Smart_Software\laravel_xiaomi_bot\Python\main.py "' . $path . '/' . $sync . '"');
                $this->subFolderSync($path . '/' . $sync);
            }
        }
    }
}
