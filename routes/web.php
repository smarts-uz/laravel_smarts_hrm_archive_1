<?php

use App\Services\FileSystemService;
use App\Services\MadelineProto\MTProtoService;
use App\Services\ManageService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use SergiX44\Nutgram\Nutgram;
use TCG\Voyager\Facades\Voyager;

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

Route::get('/preview', function () {


    $MTProto = new MTProtoService();
    $export = new \App\Services\MadelineProto\ExportService();
    $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => 1244414566, 'limit' => 20]);
//    print_r($messages);
    $tgJson = $export->ForwardJson($messages['messages']);
    print_r($tgJson);

    //    $chat = $MTProto->MadelineProto->channels->getFullChannel(['channel' => -1001807426588]);
//        $date = "6.1.2009 13:00+01:00";
//        echo '<pre>';
});

Route::get('/test', function () {
    $file_system = new FileSystemService();
    $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
    $path = '/Users/ramziddinabdumominov/Desktop/Nutgram Sync';
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

Route::get('/files', function () {
    /*$MTProto = new MTProtoService();

    $history = $MTProto->MadelineProto->messages->getHistory(['peer' => 1307688882, 'offset_date'=> 1668366000, 'limit'=>100]);
    $messages = [];
    foreach ($history['messages'] as $message) {
        if($message['date']>1668279600){
            array_push($messages, $message);
        }
    }
    echo '<pre>';
    print_r($messages);*/
//    date_default_timezone_set('UTC');
    $unix_date = 1668497340;
    $date = date("M d Y H:i:s", $unix_date);
    print_r($date);

});

Route::get('/export', function () {
    $date_start = '20.10.2022';
    $date = date_parse_from_format("j.n.Y H:iP", $date_start);

    print_r($date['year']);
});

Route::get('/madeline', function (){
    Artisan::call('manage:user');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
