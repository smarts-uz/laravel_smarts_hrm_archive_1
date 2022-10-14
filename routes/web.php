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


Route::get('/scan', function () {
    $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
    $file_system = new FileSystemService();
    $python_service = new PythonService();
    $path = '/Users/ramziddinabdumominov/Desktop/Nutgram Sync';

    $txt_file = $file_system->searchForTxt($path);
    $txt_data = $file_system->readTxt($txt_file);

    if (count(explode(' | ', $txt_data[0])) > 1 && (int)$txt_data[1] != 0) {
        $titles = [];
        $folders = scandir($path);
        foreach ($folders as $folder) {
            if (is_file($path . '/' . $folder)) {
                $post_url = $python_service->searchForMessageMac($txt_data, $titles);
                $file_system->createUrlFile($path, $post_url);
            } else {
                $file_system->createPost($path . '/' . $folder, $txt_data, $titles);
            }
        }


        foreach ($folders as $folder) {
            if (is_dir($path . '/' . $folder)) {


                //Search for channel post
                //if true create URL file
            }
        }


    }
});

Route::get('/test', function () {
    $file_system = new FileSystemService();
    $bot = new NutgramService();
    $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
    $path = 'D:\Nutgram Sync';
    $files = $file_system->fileExists($path);
    if ($files === 1) {
        $all_txt = $file_system->searchForTxt($path);
        $file = $file_system->readTxt($all_txt);
        if (count(explode(' | ', $file[0])) > 1 && (int)$file[1] != 0) {
            $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$file[1] . '::' . $file[0] . '"');
            if ($getUrl === "Message not Found") {
                $bot->sendMessage($file[0], ['chat_id' => $file[1]]);
                $getUrl = exec('D:\Nutgram_Sync_Components\venv\Scripts\python.exe D:\Nutgram_Sync_Components\search.py "' . (string)$file[1] . '::' . $file[0] . '"');
            }
            $file_system->createUrlFile($path, (string)$getUrl);
        }

    }
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
