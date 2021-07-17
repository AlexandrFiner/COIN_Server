<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VkMiniApps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Проверка параметров запуска
        $header = $request->header('Authorization');

        $client_secret = env('VK_SECRET'); //Защищённый ключ из настроек вашего приложения

        $query_params = [];
        parse_str(parse_url($header, PHP_URL_QUERY), $query_params); // Получаем query-параметры из URL

        $sign_params = [];
        foreach ($query_params as $name => $value) {
            if (strpos($name, 'vk_') !== 0) { // Получаем только vk параметры из query
                continue;
            }

            $sign_params[$name] = $value;
        }

        ksort($sign_params); // Сортируем массив по ключам
        $sign_params_query = http_build_query($sign_params); // Формируем строку вида "param_name1=value&param_name2=value"
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, $client_secret, true)), '+/', '-_'), '='); // Получаем хеш-код от строки, используя защищеный ключ приложения. Генерация на основе метода HMAC.

        $status = $sign === $query_params['sign']; // Сравниваем полученную подпись со значением параметра 'sign'

        if(!$status) {
            return response()->json([
                "error" => [
                    "error_code" => 403,
                    "error_message" => "You have wrong bearer token"
                ]
            ], 403);
        }

        // return response()->json([$header], 200);

        return $next($request);
    }
}
