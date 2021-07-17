<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function auth(Request $request) {
        try {
            $user = User::findOrFail($request['id'])->makeVisible(['api_token']);
        } catch (\Exception $e) {
            return response()->json([
                "error" => [
                    'error_code' => 404,
                    'error_message' => $e
                ]
            ], 404);
        }
        $token = Str::random(60);
        $user->update([
            "api_token" => $token
        ]);
        return response()->json([
            "response" => $user
        ]);
    }

    public function register(Request $request) {
        // Создание пользователя

        try {
            $user = User::create([
                'login' => $request['login'],
                'password' => $request['password'],
                'provider' => $request['provider'],
                'api_token' => Str::random(60)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'error_code' => 100,
                    'error_message' => $e
                ]
            ], 403);
        }

        return response()->json([
            "response" => $user
        ], 200);
    }

    public function get(Request $request) {
        // Получение информации о пользователе

        try {
            $user = User::findOrFail($request['id']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'error_code' => 404,
                    'error_message' => $e
                ]
            ], 404);
        }

        return response()->json([
            "response" => $user
        ], 200);
    }
}
