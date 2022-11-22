<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\MadelineProto\MTProtoService;
use App\Services\MadelineProto\SyncService;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync';

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
        $file_system = new FileSystemService();
        $MTProto = new SyncService();
        $path = setting('file-system.path_to_sync');
        //ALL.txt
        $txt_file = $file_system->searchForTxt($path);
        $txt_data = $file_system->readTxt($txt_file);
        // Verifying ALL.txt data
        if (count(explode(' | ', $txt_data[0])) > 1 && (int)$txt_data[1] != 0) {
            $folders = scandir($path);
            foreach ($folders as $folder) {
                $titles = [];
                if (is_dir($path . '/' . $folder) && $folder != '- Theory' && !str_starts_with($folder, '@') && !str_starts_with($folder, '.')) {
                    //Adding folder name to Title
                    array_push($titles, $folder);
                    $file_system->createPost($path . '/' . $folder, $txt_data, $titles);
                    $file_system->syncSubFolder($path . '/' . $folder, $txt_data, $titles);
                }
            }

            $progressbar = $this->output->createProgressBar();
            $progressbar->start();
            $MTProto->syncSubFolder($path);

        } else {
            print_r('TXT file type is not supported!');
            print_r('Shutting down.');
        }
    }
}
