<?php

namespace App\Console\Commands;

use App\Models\Camera;
use App\Services\NutgramService;
use Illuminate\Console\Command;
use App\Models\Office;
use SergiX44\Nutgram\Nutgram;

class CameraIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xiaomi:id {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'xiaomi:bot dan so\'ng bo\'sh joy tashlab office idsini kiriting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        $services = new NutgramService();
        $cameras = $services->getOfficeCameras((int) $id);
//        var_dump($cameras);
        foreach ($cameras as $camera) {
            print_r('Camera: ' . $camera->title);
            print_r(PHP_EOL);
            $services->getActualData($camera);
        }
    }
}
