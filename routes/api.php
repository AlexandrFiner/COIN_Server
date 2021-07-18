<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\TopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Авторизация
Route::middleware('cors', 'vkminiapps', 'throttle:20')->post('/users.auth', [UserController::class, 'auth']);
// Регистрация
Route::middleware('cors', 'vkminiapps', 'throttle:20')->post('/users.register', [UserController::class, 'register']);

// Получаем топ
Route::middleware('cors', 'throttle:20')->post('/top.get', [TopController::class, 'get']);

// Получение данных по ID
Route::middleware('cors', 'token', 'throttle:120')->post('/users.get', [UserController::class, 'get']);
// Заработок
Route::middleware('cors', 'token', 'throttle:240')->post('/users.earn', [UserController::class, 'earn']);
