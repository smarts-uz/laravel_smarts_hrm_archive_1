<?php

namespace App\Console\Commands;


use App\Services\MadelineProto\ExportService;
use App\Services\MadelineProto\MTProtoService;
use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $export = new ExportService();
        $MTProto = new MTProtoService();

        $update = [];
        $channel_id = readline('Enter a Chat ID: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $unix_start = strtotime($date_start);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);

        //$update = $this->getMessages($channel_id, $unix_start, $unix_start + 86400);
        while ($unix_end > $unix_start) {
            if ($date['hour'] == "") {
                if ($unix_start + 86400 <= $unix_end) {
                    $unix_start += 86400;
                    $date = date_parse_from_format("j.n.Y H", gmdate("j.n.Y", $unix_start));
                    $update = $export->getMessages($channel_id, $unix_start, $unix_start + 86400);
                    $files = $MTProto->getFiles($update);
                    $path = $export->folderPath($channel_id, 'D:/JSONs/', $date);
                    if(!is_dir($path . '/files')){
                        mkdir($path . '/files');
                    }
                    foreach ($files as $file){
                        $MTProto->downloadMedia($update,$file, $path . '/files/');
                    }
                    file_put_contents($path . 'result.json', json_encode($update));


                }
            } else {
                if ($unix_start + 3600 <= $unix_end) {
                    $unix_start += 3600;
                    print_r(gmdate("j.n.Y H:i", $unix_start));
                    $update = $export->getMessages($channel_id, $unix_start, $unix_start + 3600);
                    $files = $MTProto->getFiles($update);
                    $path = $export->folderPath($channel_id, 'D:/JSONs/', $date);
                    if(!is_dir($path . '/files')){
                        mkdir($path . '/files');
                    }
                    foreach ($files as $file){
                        $MTProto->downloadMedia($update,$file, $path . '/files/');
                    }
                    $date = date_parse_from_format("j.n.Y H:i", gmdate("j.n.Y H:i", $unix_start));
                    $path = $export->folderPath($channel_id, 'D:/JSONs/', $date);
                    file_put_contents($path . 'result.json', json_encode($update));
                }
            }
        }
    }
}

