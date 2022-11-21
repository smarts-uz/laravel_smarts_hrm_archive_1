<?php

use App\Services\CmdService;
use App\Services\FileSystemService;
use App\Services\MadelineProto\Database\ChatService;
use App\Services\MadelineProto\Database\UserService;
use App\Services\MadelineProto\ExportService;
use App\Services\MadelineProto\MTProtoService;
use App\Services\ManageService;
use App\Services\TestConversation;
use danog\MadelineProto\Wrappers\Webhook;
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
Route::get('/', function (){
    $cmd = new CmdService();
    $cmd->getCmd();
});

Route::post('/hook', [ManageService::class, 'handle']);

Route::get('/telegram', function () {
    $MTProto = new MTProtoService();
    $chat = $MTProto->MadelineProto->getPwrChat(-1001807426588);
    $chat2 = $MTProto->MadelineProto->getPwrChat(-1001732713545);
    echo '<pre>';
    print_r($chat);
    echo '<pre>';
    print_r($chat2);
    /*
    $chat = $MTProto->MadelineProto->getPwrChat(5305886229);
    echo '<pre>';
    print_r($chat);*/
});

Route::get('/preview', function () {
    $MTProto = new MTProtoService();
    $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => -1001807426588, 'limit' => 50]);
    echo '<pre>';
    print_r($messages);
});

Route::get('/private', function (){
    $MTProto = new MTProtoService();
    $chat = $MTProto->MadelineProto->messages->getHistory(['peer' => 798946526, 'limit' => 50]);
    echo '<pre>';
    print_r($chat);
});

Route::get('/group', function () {
    $MTProto = new MTProtoService();
    $messages = $MTProto->MadelineProto->messages->getHistory(['peer' => -1001732713545, 'limit' => 50, 'offset_date'=>1668106800]);
    echo '<pre>';
    print_r($messages['messages']);
});

Route::get('/test', function () {



    $cmd = new CmdService();
    $cmd->getCmd();

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
