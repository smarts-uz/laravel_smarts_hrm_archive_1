<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\TgController;
use App\Models\Camera;
use App\Services\FileSystemService;
use App\Services\NutgramService;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/folder', function () {
    $file_system = new FileSystemService();
    $array = $file_system->scanCurFolder('D:\Nutgram Sync');
    dd($array);
});

Route::get('/test', function () {
    $file_system = new FileSystemService();
    $bot = new NutgramService();
    $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
//    $TEST = ' Nutgram | Laravel';
//    $Q =  explode(' | ', $TEST);
//    dd(count($Q));
    $path = 'D:\Nutgram Sync';
    $files = $file_system->fileExists($path);
    if($files === 1){
        $all_txt = $file_system->searchForTxt($path);
        $file = $file_system->readTxt($all_txt);
        if(count(explode(' | ', $file[0]))>1 && (int)$file[1] != 0){
            $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$file[1] . '::' . $file[0] . '"');
            if($getUrl === "Message not Found"){
                $bot->sendMessage($file[0], ['chat_id' => $file[1]]);
                $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$file[1] . '::' . $file[0] . '"');
            }
            $file_system->createUrlFile($path, (string)$getUrl);
        }

    }

//    if((int)$file[1] != 0){
//        $url = exec('PythonSearch');
//        if($url != null){
//            $file_system->createUrl($url);
//
//        }else{
//         $bot->sendMessage((int)$file[1], $file[0]);
//        }
//    }else{
//        return null;
//    }
//    var_dump((int)$file[0]);
//    var_dump((int)$file[1]);
//    dd($file);
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
