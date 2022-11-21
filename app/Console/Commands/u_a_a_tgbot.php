<?php

namespace App\Console\Commands;

use App\Models\Camera;
use App\Models\Office;
use App\Services\ProcessCameraService;
use App\Services\TgBotService;
use Illuminate\Console\Command;

class u_a_a_tgbot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'u_a_a_tgbot:uploader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SmartsHRM telegram bot BotName';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allOffices = Office::all();
        echo "Offices list".PHP_EOL;
        foreach ($allOffices as $office) {
            echo $office->id.' - '.$office->name.' Adress: '.$office->adress.'. (phone: '.$office->phone.')'.PHP_EOL;
        }
        echo '==========================================';
        echo PHP_EOL;
        $fh = fopen('php://stdin', 'r');
        echo 'Enter the office Id:';
        $officeID = trim(fgets($fh));
        fclose($fh);

        echo '==========================================';
        echo PHP_EOL;
        echo 'Office cameras'. PHP_EOL;

        $allCameras = Camera::where('office_id', $officeID)->get();;
        foreach ($allCameras as $camera) {
            echo $camera->id.' - '.$camera->name.PHP_EOL;
        }

        $fh = fopen('php://stdin', 'r');
        echo 'Process camero No:';
        $cameraID = trim(fgets($fh));
        fclose($fh);
        $test = new ProcessCameraService($cameraID);
        //$cameraID = $test->readConsoleLine('Enter the camera ID: ');
        $path = $test->getVideoFilelist();
        dd($path);
        return Command::SUCCESS;
    }
}
