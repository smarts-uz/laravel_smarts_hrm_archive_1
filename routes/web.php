<?php

use App\Services\FileSystemService;
use App\Services\MadelineProto\ExportService;
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
    $MTProto = new MTProtoService();
    $chat = $MTProto->MadelineProto->getPwrChat(-1001732713545);
    echo '<pre>';
    print_r($chat);
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
    $export = new ExportService();
    $channel_id = 1244414566;
    $date_start = '1.11.2022';
    $date_end = '15.11.2022';
    print_r('salfasef');
    $unix_end = strtotime($date_end == "" ? "now" : $date_end);
    $unix_start = strtotime($date_start);
    $date = date_parse_from_format("j.n.Y H:iP", $date_start);
    while ($unix_end > $unix_start) {
        if ($date['hour'] == "") {
            if ($unix_start + 86400 <= $unix_end) {
                $update = $export->getMessages($channel_id, $unix_start, $unix_start + 86400);
                $date = date_parse_from_format("j.n.Y H", date("j.n.Y", $unix_start));
                $path = $export->folderPath($channel_id, '/Users/ramziddinabdumominov/Desktop/MadeLineProtoTest/test/', $date);
                if (!is_dir($path . '/files')) {
                    mkdir($path . '/files');
                }
                file_put_contents($path . 'result.json', json_encode($update));
                $telegram = $export->ForwardJson($update);
                file_put_contents($path . 'telegram.json', json_encode($telegram));
                $unix_start += 86400;
                foreach ($update as $messa) {
                    if (array_key_exists('media', $messa)) {
                        if (array_key_exists('document', $messa['media'])) {
                            try {
                                foreach ($messa['media']['document']['attributes'] as $attribute) {
                                    if ($attribute['_'] == 'documentAttributeFilename') {
                                        $export->MTProto->MadelineProto->downloadToDir($messa['media'], $path . '/');
                                        print_r('Downloading ' . $attribute['file_name']);
                                        print_r(PHP_EOL);
                                    }
                                }
                            } catch (\Exception $e) {
                                print_r($e->getMessage());
                            }

                        }
//                        yield $export->MTProto->MadelineProto->downloadToDir($messa['media'], $path . '/');
                    }
                }
            }
        }
    }
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
