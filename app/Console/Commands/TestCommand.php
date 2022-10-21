<?php

namespace App\Console\Commands;

use App\Services\FileSystemService;
use App\Services\NutgramService;
use App\Services\PythonService;
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
        $file_system = new FileSystemService();
        $path = 'D:\Smart_Software\Sync_Data\PHP';
        //ALL.txt
        $txt_file = $file_system->searchForTxt($path);
        $txt_data = $file_system->readTxt($txt_file);
        // Verifying ALL.txt data
        $python_service = new PythonService();
        if (count(explode(' | ', $txt_data[0])) > 1 && (int)$txt_data[1] != 0) {
            $folders = scandir($path);
            foreach ($folders as $folder) {
                $titles = [];
                if (is_dir($path . '/' . $folder) && $folder != '- Theory' && !str_starts_with($folder, '@')
                    && !str_starts_with($folder, '.')) {
                    //Adding folder name to Title
                    array_push($titles, $folder);
                    $file_system->createPost($path . '/' . $folder, $txt_data, $titles);
                    $file_system->syncSubFolder($path . '/' . $folder, $txt_data, $titles);
                }else if($folder == '- Theory'){
                    $urls = $python_service->searchForMessage($txt_data, $titles = [ 'ALL']);
                    $file_system->createUrlFile($path . '/- Theory', $urls);
                }
            }
        }

        $python_service->subFolderSync($path);
    }
}
