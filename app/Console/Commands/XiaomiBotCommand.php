<?php

namespace App\Console\Commands;

use App\Models\Camera;
use App\Services\NutgramService;
use Illuminate\Console\Command;
use App\Models\Office;
use SergiX44\Nutgram\Nutgram;

class XiaomiBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xiaomi:bot';

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
        $services = new NutgramService();
        $cameras = $services->getCameraList();
        foreach ($cameras as $camera) {
            print_r('Camera: ' . $camera->title);
            print_r(PHP_EOL);
            $services->getActualData($camera);
        }

        /*
                $user = 'share';
                $password = 'admin123456';







                // Log In
                exec('net use "\\\192.168.100.100" /user:"' . $user . '" "' . $password . '" /persistent:no');
                $bot = new Nutgram("5743173293:AAF33GAKELp-Id9y00EhIJRrpWI37umZ788");
                $camera_folder = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos/' . $camera_id);
                for ($i = array_search($last_folder, $camera_folder) + 1; $i < count($camera_folder) - 2; $i++) {
                    $path = 'Z:/xiaomi_camera_videos/' . $camera_id . '/' . $camera_folder[$i];
                    $current_dir = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos/' . $camera_id . '/' . $camera_folder[$i]);
                    print_r('Folder: ');
                    print_r($camera_folder[$i]);
                    print_r(PHP_EOL);
                    print_r('Files: ');
                    print_r(PHP_EOL);
                    if ($i == array_search($last_folder, $camera_folder) + 1) {
                        for ($o = array_search($last_video, $current_dir) + 1; $o <= count($current_dir) - 2; $o++) {
                            print_r($current_dir[$o]);
                            print_r(PHP_EOL);
                            $video = fopen($path . '/' . $current_dir[$o], 'r+');
                            $bot->sendDocument($video, ['chat_id' => '-1001626673572', 'reply_to_message_id' => '11', 'caption' => $current_dir[$o]]);
                            sleep(0.2);

                        }
                    } else {
                        for ($o = 0; $o <= count($current_dir) - 2; $o++) {
                            print_r($current_dir[$o]);
                            print_r(PHP_EOL);

                        }
                    }
                }
            }*/
    }
}
