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
        $header = $request->header('Authorization');

        $client_secret = env('VK_SECRET');

        $query_params = [];
        parse_str(parse_url($header, PHP_URL_QUERY), $query_params);

        $sign_params = [];
        foreach ($query_params as $name => $value) {
            if (strpos($name, 'vk_') !== 0)
                continue;

            $sign_params[$name] = $value;
        }

        if(!isset($query_params['sign'])) {
            return response()->json([
                "error" => [
                    "error_code" => 403,
                    "error_message" => "You have wrong bearer token"
                ]
            ], 403);
        }

        ksort($sign_params);
        $sign_params_query = http_build_query($sign_params);
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, $client_secret, true)), '+/', '-_'), '=');

        if($sign !== $query_params['sign']) {
            return response()->json([
                "error" => [
                    "error_code" => 403,
                    "error_message" => "You have wrong bearer token"
                ]
            ], 403);
        }

        $user = $query_params['vk_user_id'];        // Кто сделал запрос
        $request->merge(['user' => $user ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
