<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\TgController;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;

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

Route::match(['post', 'get'],'/', [TgController::class, 'index']);

Route::get('/scan', [Controller::class, 'scanDir']);


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
