<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http;

class Response extends Model
{
    use HasFactory;

    public static function success($data = [], $code = 200): Http\JsonResponse
    {
        return response()->json([
            'response' => $data
        ], $code);
    }

    public static function error($error_code = 400, $error_message = "", $code = 200): Http\JsonResponse
    {
        return response()->json([
            'error' => [
                "error_code" => $error_code,
                "error_message" => $error_message
            ]
        ], $code);
    }
}
