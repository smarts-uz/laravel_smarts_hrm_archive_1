<?php

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Services\FileSystemService;
use App\Services\ManageService;
use App\Services\NutgramService;
use App\Services\PythonService;
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

Route::post('/hook', [ManageService::class, 'handle']);

Route::get('/telegram', function () {
    $filename = 'D:/ALL.url';
    $handle = fopen($filename, "r");
    $contents = file($filename);;
    return $contents;
});



Route::get('/proto', function ()  {

    $MTProto = new \App\Services\MTProtoService();

    $comments = $MTProto->getComments('https://t.me/c/1807426588/408');
    dd($comments);
});

Route::get('/test', function () {
    $file_system = new FileSystemService();
    $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
    $path = 'D:\Smart_Software\Sync_Data\PHP';
    $files = $file_system->fileExists($path);
    if ($files === 1) {
        $all_txt = $file_system->searchForTxt($path);
        $file = $file_system->readTxt($all_txt);
        $titles = [];
        $file_system->syncSubFolder($path, $file, $titles);
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
