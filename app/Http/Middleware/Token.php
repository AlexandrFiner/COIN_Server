<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class Token
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
        try {
            $user = User::where('api_token', '=', $header)->firstOrFail();

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'error_code' => 403,
                    'error_message' => 'Wrong api token'
                ]
            ], 403);
        }
        $request->merge(['user' => $user ]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

// if you dump() you can now see the $request has it
        // var_dump($request->user());

        return $next($request);
    }
}
