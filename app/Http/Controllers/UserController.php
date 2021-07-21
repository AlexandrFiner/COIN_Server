<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function auth(Request $request) {
        try {
            $user = User::where('login', '=', $request->user())->firstOrFail()->makeVisible(['api_token']);
        } catch (\Exception $e) {
            return response()->json([
                "error" => [
                    'error_code' => 404,
                    'error_message' => 'Account does`nt exists'
                ]
            ], 200);
        }

        $data = $request->request->all();
        $group_id = 0;
        if(isset($data['group'])) {
            // Если играет через группу, проверяем на существование в базе такой группы
            $group_id = $data['group']['id'];

            try {
                $group = Group::where('group_id', '=', $group_id)->firstOrFail();
            } catch (\Exception $e) {
                // Группы такой нет
                if(isset($data['group']['isAdmin'])) {
                    Group::create([
                        'group_id' => $group_id,
                        'admin_id' => $user->id,
                        'balance_coin' => 0
                    ]);
                } else {
                    $group_id = 0;
                    // тут будем писать, что группы такой еще нет
                }
            }
        }

        $token = Str::random(60);
        $user->update([
            "api_token" => $token,
            "group_vk" => $group_id
        ]);

        return response()->json([
            "response" => $user
        ], 200);
    }

    public function register(Request $request) {
        // Создание пользователя

        try {
            $user = User::create([
                'login' => $request->user(),
                'password' => Str::random(60),        // Пароль
                'provider' => 'vk',                         // Пока только VK
                'api_token' => Str::random(60)        // Создаем ключ API
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'error_code' => 100,
                    'error_message' => 'Account already exists'
                ]
            ], 200);
        }

        return response()->json([
            "response" => $user->makeVisible(['api_token'])
        ], 200);
    }

    public function get(Request $request) {
        // Получение информации о пользователе

        if(isset($request['id'])) {
            try {
                $user = User::findOrFail($request['id']);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => [
                        'error_code' => 404,
                        'error_message' => $e
                    ]
                ], 200);
            }
        } else
            $user = $request->user();

        return response()->json([
            "response" => $user
        ], 200);
    }

    public function earn(Request $request) {
        // Заработок
        $params = $request->all();

        if(!isset($params['hash'])) {
            return response()->json([
                'error' => [
                    'error_code' => 1000,
                    'error_message' => "Wrong hash!"
                ]
            ], 200);
        }

        $hash = hash('sha256', $request->user()->api_token.'#CRYPT#alexfiner');

        if($hash !== $params['hash']) {
            return response()->json([
                'error' => [
                    'error_code' => 1000,
                    'error_message' => "Wrong hash!"
                ]
            ], 200);
        }

        try {
            $token = Str::random(60);
            $request->user()->update([
                "api_token" => $token,
                'balance_coin' => $request->user()->balance_coin + $request->user()->mining_speed
            ]);
            // $user = User::
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'error_code' => 404,
                    'error_message' => $e
                ]
            ], 200);
        }
        return response()->json([
            "response" => $request->user()->makeVisible(['api_token']),
        ], 200);
    }
}
