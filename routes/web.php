<?php

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Services\FileSystemService;
use App\Services\ManageService;
use App\Services\NutgramService;
use App\Services\PythonService;
use App\Services\SearchService;
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
Route::get('/history', function () {
    $MTProto = new \App\Services\MTProtoService();
    /*try{
        $MTProto->sync('D:\Smart_Software\Sync_Data\PHP\PHPython');
    }catch (Exception $e){
        dump($e->getMessage());
    }*/
    $qwe = $MTProto->MadelineProto->messages->sendMessage(['peer' => '@Ramziddin_dev', 'message' => '#post_url']);
    print_r($qwe);
});

Route::post('/hook', [ManageService::class, 'handle']);

Route::get('/telegram', function () {
    $filename = 'D:/ALL.url';
    $handle = fopen($filename, "r");
    $contents = file($filename);;
    return $contents;
});

Route::get('/search', function () {
    $MTProto = new \App\Services\MTProtoService();
    $envato = new \App\Services\EnvatoService();
    $search = new SearchService();
    $send = new \App\Services\SendMediaServis();

    $offset = 'https://t.me/c/1807426588/532';
    $end = 'https://t.me/c/1807426588/534';

    if ($end == null) {
        return;
    } else {
        for ($i = (int)explode('/', $offset)[5]; $i <= (int)explode('/', $end)[5]; $i++) {
            $message = $MTProto->getReplyMessage(substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
            $mess_url = $search->searchMessage(-1001732713545, $message);
            print_r(substr($offset, 0, -strlen(explode('/', $offset)[5])));
            print_r($i);
            print_r(PHP_EOL);
            $comments = $MTProto->getComments(substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
            if (count($comments) == 0) {
                $split = explode("/", substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
                $replies = $MTProto->MadelineProto->messages->getHistory(['peer' => '-100' . $split[4], 'offset_id' => (int)$split[5] + 1]);
                print_r($replies['messages'][0]['message']);
                $link = $envato->getLink($replies['messages'][0]['message']);
                print_r($link);
                $messages_DiscussionMessage = $MTProto->MadelineProto->messages->getDiscussionMessage(['peer'=> '-100' . $split[4], 'msg_id'=> (int)$split[5]]);
                print_r($messages_DiscussionMessage['messages'][0]['peer_id']['channel_id']);
                print_r($messages_DiscussionMessage['messages'][0]['id']);
                $qwe = $MTProto->MadelineProto->messages->sendMedia(['peer' => '-100' . $messages_DiscussionMessage['messages'][0]['peer_id']['channel_id'],"media" =>
                    ['_' => 'inputMediaUploadedDocument','file' => $link[0]], "message" => '#post_file', 'reply_to_msg_id'=>(int)$messages_DiscussionMessage['messages'][0]['id']]);
                print_r($qwe);
            }
            foreach ($comments as $comment) {

                if (!str_contains($comment['message'], "#post_file")) {
                        $split = explode("/", substr($offset, 0, -strlen(explode('/', $offset)[5])) . $i);
                        $replies = $MTProto->MadelineProto->messages->getHistory(['peer' => '-100' . $split[4], 'offset_id' => (int)$split[5] + 1]);
                        $link = $envato->getLink($replies['messages'][0]['message']);
                        print_r($link);
                }
            }
        }
    }
});

Route::get('/proto', function () {

    $MTProto = new \App\Services\MTProtoService();


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
