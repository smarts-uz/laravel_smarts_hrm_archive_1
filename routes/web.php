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


Route::get('/view', function () {
    $file_system = new FileSystemService();
    $message = [
        "message_id"=> 4,
"from"=> [
        "id"=> 1244414566,
"is_bot"=> false,
"first_name"=> "Anthony Akbar",
"username"=> "akbarshoh8522",
"language_code"=> "ru"
],
"chat"=> [
        "id"=> 1244414566,
"first_name"=> "Anthony Akbar",
"username"=> "akbarshoh8522",
"type"=> "private"
],
"date"=> 1665033366,
"text"=> "svsdvsv"
];
    return $file_system->createUrl('D:/',$message);
});

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

Route::get('/folder', function () {
    $file_system = new FileSystemService();
    $array = $file_system->scanCurFolder('D:/Anthony Akbar/Documents');
    dd($array);
});

Route::get('/chat', function () {

    /*$file_system = new FileSystemService();
    $message_id = $nutgram->getMessageId('TestSyncFolder');
    $files = $file_system->TelegramWanted('D:\PHP');
    $file_system->sendToTelegram('D:/PHP', $files, $message_id);*/
    $nutgram = new NutgramService();
    $file_system = new FileSystemService();
    $array = $file_system->scanCurFolder('D:/TG/PHP');
//    dd($array);


    foreach ($array as $item) {

        /*else if (is_array($item)) {
            $index = array_search($item, $array);

            // Check url file
            //Create Post in Channel
            //Upload to Comments
        }*/
    }

    /*$message = $nutgram->getGroupMessageId('D:/TG/PHP');
                    $nutgram->sendFileToComments('D:/TG/PHP', $item, $message->message_id);*/
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
