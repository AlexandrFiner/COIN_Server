<?php

namespace App\Http\Middleware;

use App\Models\Api\Response;
use Closure;
use Illuminate\Http\Request;

class Moderator
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
        if($request->user()->role !== 'admin' && $request->user()->role !== 'moderator' )
            return Response::error(403, "You have no access to this url" , 403);

        return $next($request);
    }
}
