<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;

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
Route::middleware('vkminiapps')->post('/users.auth', [UserController::class, 'auth']);
// Регистрация
Route::middleware('vkminiapps')->post('/users.register', [UserController::class, 'register']);
// Получение данных по ID
Route::middleware('token')->post('/users.get', [UserController::class, 'get']);
