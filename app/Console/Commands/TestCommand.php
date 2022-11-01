<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\MTProtoService;
use App\Services\NutgramService;
use App\Services\PythonService;
use Exception;
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
        $MTProto = new \App\Services\MTProtoService();


        try {
            $MTProto->sync('D:\Smart_Software\Sync_Data\PHP\Tequilarapido.Python-Bridge');
        } catch (Exception $e) {
            dump($e->getMessage());
        }

    }
    }
