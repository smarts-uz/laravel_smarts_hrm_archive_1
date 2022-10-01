<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Zanzara\Zanzara;
use Zanzara\Context;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function handle(){

        $bot = new Zanzara("5489678108:AAHbMYiTSjFe-w6G-TGwIePfnzbovfKWxQc");

        $bot->onCommand('start', function (Context $ctx) {
            $userId = $ctx->getMessage()->getFrom()->getId();
            $photos = $ctx->getUserProfilePhotos($userId);
            $ctx->sendMessage(print_r($photos));
        });

        $bot->run();

    }

    public function scanDir(){
        dd($this->service->getCameraList());
        /*$dir = scandir('smb://share:share@192.168.100.100/Records/xiaomi_camera_videos');
        dd($dir);*/
//        echo phpversion();


        $user = env('SHARED_FOLDER_USER');
        $password = env('SHARED_FOLDER_PASSWORD');

        exec('net use "\\\192.168.100.100" /user:"' . $user . '" "' . $password . '" /persistent:no');
        $files = scandir('\\\192.168.100.100/Records/xiaomi_camera_videos');
        echo '<pre>';
        print_r($files);
    }
}
