<?php

namespace App\Console\Commands;

use App\Models\Camera;
use Illuminate\Console\Command;
use App\Models\Office;

class XiaomiBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xiaomi:bot {office_id}';

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

        $id = $this->argument('office_id');
        $cameras = Camera::where('office_id', $id)->get();
        $length = count($cameras);
        print_r($cameras);
        die();
        for ($i = 0; $i <= $length - 1; $i++){
            print_r($cameras[$i]['title']);
            echo "\n";
//            echo $cameras[$i]->title."\n";
        }


        /*$user = 'share';
        $password = 'admin123456';

        exec('net use "\\\192.168.100.100" /user:"'.$user.'" "'.$password.'" /persistent:no');

        $last_folder = '2022093014';
        $camera_id = '44237c9659a3';
        $last_video = '28M56S_1664533736.mp4';
        $camera_folder = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos/' . $camera_id);

        for($i = array_search($last_folder, $camera_folder)+1; $i<count($camera_folder)-2; $i++){
            print_r('Folder: ');
            print_r($camera_folder[$i]);
            print_r(PHP_EOL);
        }*/
    }
}
