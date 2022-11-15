<?php

namespace App\Services\MadelineProto;

class ExportService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function getMessages($id, $start, $end)
    {
        $messages = $this->MTProto->MadelineProto->messages->getHistory(['peer' => $id, 'offset_date' => $end, 'limit' => 100]);
        $update = [];
        foreach ($messages['messages'] as $message) {
            if ($message['date'] > (int)$start) {
                array_push($update, $message);
            }
        }
        return $update;
    }

    public function downloadMedia($messages, $path)
    {
        print_r(111111);
        if (!is_dir($path . 'files')) {
            mkdir($path . 'files');
        }
        $path .= 'files/';
        foreach ($messages as $message) {
            if (array_key_exists('media', $message)) {
                try {
                    print_r('Downloading ' . $message['media']['document']['attributes'][0]['file_name']);
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                }
                yield $this->MTProto->MadelineProto->downloadToDir($message['media'], $path . '/');
            }
        }
    }

    public function folderPath($id, $path, $date)
    {

        //$chat = $this->MTProto->MadelineProto->channels->getFullChannel(['channel' => $id]);

        //Title
        if (!is_dir($path . $id)) {
            mkdir($path . $id);
        }
        $path .= $id . '/';

        //Year
        if (!is_dir($path . $date['year'])) {
            mkdir($path . $date['year']);
        }
        $path .= $date['year'] . '/';


        //month
        if (!is_dir($path . $date['month'])) {
            mkdir($path . $date['month']);
        }
        $path .= $date['month'] . '/';

        //day
        if (!is_dir($path . $date['day'])) {
            mkdir($path . $date['day']);
        }
        $path .= $date['day'] . '/';

        //Hours
        if ($date['hour'] != "") {
            if (!is_dir($path . $date['hour'])) {
                mkdir($path . $date['hour']);
            }
            $path .= $date['hour'] . '/';
        }
        return $path;
    }

    public function export()
    {

        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_start = strtotime($date_start);
        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        $path = $this->folderPath($channel_id, 'C:\Users\Pavilion\Documents\MadelineProto\JSONs\Updates\\', $date);
        if ($date['hour'] == "") {
            if ($unix_start + 86400 <= $unix_end) {
                $update = $this->getMessages($channel_id, $unix_start, $unix_start + 86400);
                file_put_contents($path . 'result.json', json_encode($update));
                $unix_start += 86400;
                print_r(gmdate("j.n.Y H:iP", $unix_start));
            }
        }
    }
}
