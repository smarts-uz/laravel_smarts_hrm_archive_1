<?php

namespace App\Console\Commands;


use App\Models\TgChat;
use App\Services\MadelineProto\ExportService;
use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export {--channelid=} {--startdate=} {--enddate=} {--path=}';

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
        if (empty($this->option('channelid'))) {
            $channel_id = readline('Enter channel_id: ');
        } else {
            $channel_id = $this->option('channelid');
        }
        if ($this->option('startdate') == "") {
            $date_start = readline('Enter start date: ');
        } else {
            $date_start = $this->option('startdate');
        }
        if ($this->option('enddate') == "") {
            $date_end = readline('Enter end date: ');
        } else {
            $date_end = $this->option('enddate');
        }
        if ($this->option('path') == "") {
            $path = setting('file-system.tg_export');
        } else {
            $path = $this->option('path');
        }
        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $unix_start = strtotime($date_start);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        $max = $date['hour'] == "" ? ($unix_end - $unix_start) / 86400 : ($unix_end - $unix_start) / 3600;
        $progressbar = $this->output->createProgressBar((int)$max);
        $progressbar->start();
        while ($unix_end > $unix_start) {
            if ($date['hour'] == "") {
                if ($unix_start + 86400 <= $unix_end) {

                    $date = date_parse_from_format("j.n.Y H", date("j.n.Y", $unix_start));
                    $end = $unix_start + 86400;
                    $export->export($channel_id, $unix_start, $end, $date, $path);
                    $unix_start += 86400;
                    $progressbar->advance(1);

                }
            } else {
                if ($unix_start + 3600 <= $unix_end) {
                    $date = date_parse_from_format("j.n.Y H:i", gmdate("j.n.Y H:i", $unix_start));
                    $end = $unix_start + 3600;
                    $export->export($channel_id, $unix_start, $end, $date, $path);
                    $unix_start += 3600;
                    $progressbar->advance(1);

                }
            }
        }
        //$export->MTProto->MadelineProto->stop();
    }

    public function handle__()
    {
        echo 'sdafsda';

            /** @var TgChat $className */
            // $className = 'App\Models\TgChat';

            //$type = 'User';
            $type = 'Chat';
            $className = 'App\Models\Tg' . $type;

            $channels_id = $className::pluck('tg_id');

        echo 'sdafsda';

    }

}

