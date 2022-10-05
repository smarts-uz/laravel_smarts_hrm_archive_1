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


Route::get('/bot', function () {

    $bot = new Nutgram(env('BOT_TOKEN'), ['timeout' => 60]);
    $chat = array();
    $updates = $bot->getUpdates();
    foreach ($updates as $update) {
        if ($update->channel_post) {
            $test = $update->channel_post;
            $qwe = $test->chat;
            if ($qwe->id == -1001827937110 && $test->text === "TestSync") {
                array_push($chat, $test);
            }
        }
    }
    dd($chat);
    return $chat;
});

Route::get('/chat', function () {

    /*$file_system = new FileSystemService();
    $nutgram = new NutgramService();
    $message_id = $nutgram->getMessageId('TestSyncFolder');
    $files = $file_system->TelegramWanted('D:\PHP');
    $file_system->sendToTelegram('D:/PHP', $files, $message_id);*/

    $file_system = new FileSystemService();
    $array = $file_system->scanCurFolder('D:/TG/PHP');
    //$array = $file_system->TelegramWanted('D:/PHP');
//    dd($array);



    foreach ($array as $item) {
        if(!is_array($item) && !is_dir('D:/TG/PHP' .'/'. $item)){
            array_push($array2,$item);

        }else if(is_array($item)){
            $index = array_search($item , $array);
        }
    }



});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
