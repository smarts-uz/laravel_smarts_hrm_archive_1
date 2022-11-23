<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadFromDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:download';

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
        $download = new \App\Services\MadelineProto\Database\DownloadMediaService();
        $messages = \App\Models\TgChatText::where('media', '!=', null)->where('media__', 'messageMediaDocument')->get();
        foreach ($messages as $message){

            $path = $download->folderPath($message, 'C:\Users\Pailion\Documents\MadelineProto\Database_media');
            $download->downloadMedia($path, $message);
            dump('Finish ' . $message->id);
        }
    }
}
