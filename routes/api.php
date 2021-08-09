<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\TopController;
use \App\Http\Controllers\AdminController;
use \App\Http\Controllers\ModeratorController;
use \App\Http\Controllers\DonutController;
use \App\Http\Controllers\ClanController;
use \App\Http\Controllers\DecorationsController;
use \App\Http\Controllers\AdvertsController;

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
 * Пользователь
 */
Route::group(['middleware' => 'vkminiapps'], function () {
    Route::post('/users.auth', [UserController::class, 'auth']);                // Авторизация
    Route::post('/users.register', [UserController::class, 'register']);        // Регистрация
});

Route::group(['middleware' => 'token'], function () {
    Route::post('/users.get', [UserController::class, 'get']);                 // Получение данных по ID
    Route::post('/users.earn', [UserController::class, 'earn']);               // Заработок
});

/*
 * Топ
 */
Route::group(['middleware' => 'token'], function () {
    Route::post('/top.get', [TopController::class, 'get']);
    Route::post('/top.get/groups', [TopController::class, 'getGroups']);
    Route::post('/top.get/clans', [TopController::class, 'getClans']);
});

/*
 * Кланы
 */
Route::group(['middleware' => 'token'], function () {
    Route::post('/clans.get', [ClanController::class, 'index']);
    Route::post('/clans.create', [ClanController::class, 'create']);
    Route::post('/clans.search', [ClanController::class, 'search']);
    Route::post('/clans.update', [ClanController::class, 'update']);
    Route::post('/clans.updateAvatar', [ClanController::class, 'uploadAvatar']);
    Route::post('/clans.getUsers', [ClanController::class, 'getUsers']);
});

/*
 * Декорации
 */
Route::group(['middleware' => 'token'], function () {
    Route::post('/decorations.get', [DecorationsController::class, 'index']);
    Route::post('/decorations.getItem', [DecorationsController::class, 'get']);
});

/*
 * Рекламные записи
 */
Route::group(['middleware' => 'token'], function () {
    // AdBlock блокирует ad/advert, используем не очень понятное, banners
    Route::post('/banners.get', [AdvertsController::class, 'get']);
});

// Админы
Route::group(['middleware' => 'token'], function () {
    Route::group(['middleware' => 'admin'], function () {
        Route::post('/admin', [AdminController::class, 'index']);
    });
});

// Модераторы
Route::group(['middleware' => 'token'], function () {
    Route::group(['middleware' => 'moderator'], function () {
        Route::post('/moderator', [ModeratorController::class, 'index']);
    });
});

// Доны
Route::group(['middleware' => 'token'], function () {
    Route::group(['middleware' => 'donut'], function () {
        Route::post('/donut', [DonutController::class, 'index']);
    });
});
