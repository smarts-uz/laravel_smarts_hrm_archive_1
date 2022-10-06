<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\NutgramService;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

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
        $nutgram = new NutgramService();
        $file_system = new FileSystemService();

        $array = $file_system->scanCurFolder('D:/Anthony Akbar/Documents');
        $nutgram->syncTelegram($array, 'D:/Anthony Akbar/Documents');
        dd($array);

        /*$post = $nutgram->getChannelPost('D:/Anthony Akbar/Documents/Koder');
        dd($post);*/
    }
}
