<?php

namespace App\Services;

use SergiX44\Nutgram\Nutgram;

class PythonService
{
//    public function searchForMessage($txt_data, $titles = [])
//    {
//        $bot = new Nutgram(env('BOT_TOKEN'), ['timeout' => 60]);
//        $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$txt_data[1] . '::' . $txt_data[0] . '"');
//        if ($getUrl === "Message not Found") {
//            if (count($titles) != 0) {
//                $text = '';
//                for ($i = count($titles); $i <= 0; $i--) {
//                    $text .= $titles[$i] . ' | ';
//                }
//                $bot->sendMessage($text . $txt_data[0], ['chat_id' => $txt_data[1]]);
//            } else {
//                $bot->sendMessage($txt_data[0], ['chat_id' => $txt_data[1]]);
//            }
//            $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$txt_data[1] . '::' . $txt_data[0] . '"');
//        }
//        return $getUrl;
//    }

    public function searchForMessageMac($txt_data, $titles = [])
    {
        $bot = new Nutgram(env('BOT_TOKEN'), ['timeout' => 60]);
        $getUrl = exec('/Users/ramziddinabdumominov/Desktop/Nutgram_Sync_Components_Mac/venv/bin/python3 /Users/ramziddinabdumominov/Desktop/Nutgram_Sync_Components_Mac/search.py "' . (string)$txt_data[1] . '::' . $txt_data[0] . '"');
//        dd($getUrl);
        if ($getUrl === "Message not Found") {
            if (count($titles) != 0) {
                $text = '';
                for ($i = count($titles); $i <= 0; $i--) {
                    $text .= $titles[$i] . ' | ';
                }
                $bot->sendMessage($text . $txt_data[0], ['chat_id' => $txt_data[1]]);
            } else {
                $bot->sendMessage($txt_data[0], ['chat_id' => $txt_data[1]]);
            }
            $getUrl = exec('/Users/ramziddinabdumominov/Desktop/Nutgram_Sync_Components_Mac/venv/bin/python3 /Users/ramziddinabdumominov/Desktop/Nutgram_Sync_Components_Mac/search.py "' . (string)$txt_data[1] . '::' . $txt_data[0] . '"');
            dd($getUrl);
        }
        return $getUrl;
    }

}
