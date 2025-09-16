<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UseRefreshTokenFromCookie
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('refresh_token') && !$request->bearerToken()) {
            $token = $request->cookie('refresh_token');
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
