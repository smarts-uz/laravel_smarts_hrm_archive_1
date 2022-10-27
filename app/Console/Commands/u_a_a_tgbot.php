<?php

namespace App\Console\Commands;

use App\Models\Office;
use App\Services\ProcessCameraService;
use App\Services\TgBotService;
use Illuminate\Console\Command;
use App\Models\Camera;

class u_a_a_tgbot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'u_a_a_tgbot:uploader {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SmartsHRM telegram bot BotName';

    public function argumentsRequest()
    {
        //Office selection block
        $allOffices = Office::all();
        echo "Offices list" . PHP_EOL;
        foreach ($allOffices as $office) {
            echo $office->id . ' - ' . $office->name . ' Adress: ' . $office->adress . '. (phone: ' . $office->phone . ')' . PHP_EOL;
        }
        echo '==========================================';
        echo PHP_EOL;
/*        $fh = fopen('php://stdin', 'r');
        echo 'Select office No:';
        $officeID = trim(fgets($fh));
        fclose($fh);*/

        $officeNumber = $this->ask('Select office No:');

        //Office cameras selection block
        echo '==========================================';
        echo PHP_EOL;
        echo 'Office cameras' . PHP_EOL;

        $allCameras = Camera::where('office_id', $officeNumber)->get();;
        foreach ($allCameras as $camera) {
            echo $camera->id . ' - ' . $camera->name . PHP_EOL;
        }

/*        $fh = fopen('php://stdin', 'r');
        echo 'Process camera No:';
        $cameraID = trim(fgets($fh));
        fclose($fh);*/

        $cameraID = $this->ask('Select camera No:');

        return $cameraID;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ( $this->argument('id') !== null) {
            $cameraID = $this->argument('id');
        }
           else {
               $cameraID = $this->argumentsRequest();
           }
        $test = new ProcessCameraService($cameraID);
        $path = $test->getVideoFilelist();

        return Command::SUCCESS;
    }
}
