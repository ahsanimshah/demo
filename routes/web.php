<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SmsController;
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

Route::get('/', function () {
    return view('welcome');
});
//Route::get('users/sendSms', [UserController::class, 'sendSms']);
//Route::resource('users', UserController::class);
Route::resource('users', UserController::class)->only([
    'index','create', 'store', 'show','destroy','edit','update','sendSms'
]);

Route::resource('sms', SmsController::class);

