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
Route::middleware('cors', 'vkminiapps')->post('/users.auth', [UserController::class, 'auth']);          // Авторизация
Route::middleware('cors', 'vkminiapps')->post('/users.register', [UserController::class, 'register']);  // Регистрация
Route::middleware('cors', 'token')->post('/users.get', [UserController::class, 'get']);                 // Получение данных по ID
Route::middleware('cors', 'token')->post('/users.earn', [UserController::class, 'earn']);               // Заработок

/*
 * Топ
 */
Route::middleware('cors')->post('/top.get', [TopController::class, 'get']);
Route::middleware('cors')->post('/top.get/groups', [TopController::class, 'getGroups']);
Route::middleware('cors')->post('/top.get/clans', [TopController::class, 'getClans']);


/*
 * Кланы
 */
Route::middleware('cors', 'token')->post('/clans.get', [ClanController::class, 'index']);
Route::middleware('cors', 'token')->post('/clans.create', [ClanController::class, 'create']);
Route::middleware('cors', 'token')->post('/clans.search', [ClanController::class, 'search']);
Route::middleware('cors', 'token')->post('/clans.update', [ClanController::class, 'update']);
Route::middleware('cors', 'token')->post('/clans.updateAvatar', [ClanController::class, 'uploadAvatar']);
Route::middleware('cors', 'token')->post('/clans.getUsers', [ClanController::class, 'getUsers']);

/*
 * Декорации
 */
Route::middleware('cors', 'token')->post('/decorations.get', [DecorationsController::class, 'index']);
Route::middleware('cors', 'token')->post('/decorations.getItem', [DecorationsController::class, 'get']);

/*
 * Рекламные записи
 */
// AdBlock блокирует ad/advert, используем не очень понятное, banners
Route::middleware('cors', 'token')->post('/banners.get', [AdvertsController::class, 'get']);

// Админы
Route::middleware('cors', 'token', 'admin')->post('/admin', [AdminController::class, 'index']);

// Модераторы
Route::middleware('cors', 'token', 'moderator')->post('/moderator', [ModeratorController::class, 'index']);

// Доны
Route::middleware('cors', 'token', 'donut')->post('/donut', [DonutController::class, 'index']);
