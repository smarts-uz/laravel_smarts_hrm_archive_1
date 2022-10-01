<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\TgController;
use App\Models\Camera;
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

Route::match(['post', 'get'], '/', [TgController::class, 'index']);

Route::get('/scan', [Controller::class, 'scanDir']);
Route::get('/camera', function () {

    $cameras = Camera::where('office_id', 1)->get();
    dd($cameras[0]->id);

});
Route::get('/bot', function () {
    $bot = new Nutgram("5743173293:AAF33GAKELp-Id9y00EhIJRrpWI37umZ788");
    $updates = $bot->getUpdates(['chat_id' => '1244414566']);
    foreach ($updates as $update) {
        if ($update->message) {
            $test = $update->message;
            if($test->chat->id == -1001626673572) {
                if ($test->text == "Hello World") {
                    $bot->sendMessage('Hello World!', ['chat_id' => '-1001626673572', 'reply_to_message_id' => $test->message_id]);
                }
            }
        }
    }
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
