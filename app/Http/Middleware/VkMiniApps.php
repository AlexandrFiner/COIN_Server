<?php

namespace App\Http\Middleware;

use App\Models\Api\Response;
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

        ksort($sign_params);
        $sign_params_query = http_build_query($sign_params);
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, $client_secret, true)), '+/', '-_'), '=');

        if(!isset($query_params['sign']))
            return Response::error(403, "You must send sign" , 200);

        if($sign !== $query_params['sign'])
            return Response::error(403, "You have wrong bearer token" , 200);


        $user = $query_params['vk_user_id'];        // Кто сделал запрос
        if(isset($query_params['vk_group_id'])) {
            $group['id'] = (int)$query_params['vk_group_id'];
            $group['isAdmin'] = false;
            if(isset($query_params['vk_viewer_group_role']) && $query_params['vk_viewer_group_role'] === 'admin') {
                // Это админ

                $group['isAdmin'] = true;
            }
            $request->request->add(['group' => $group]);
        }

        $request->merge(['user' => $user ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
