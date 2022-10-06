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
        dd(1111111);
        $file_system = new FileSystemService();
        $nutgram = new NutgramService();
        $array = $file_system->scanCurFolder('D:/TG/PHP');
        dd($array);
        $nutgram->syncTelegram('D:/TG/PHP', $array);

    }
}
