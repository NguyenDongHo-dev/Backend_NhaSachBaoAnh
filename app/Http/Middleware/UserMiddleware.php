<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {

            $payload = JWTAuth::parseToken()->getPayload();

            $userToken = $payload->get('sub');
            $idRoute = $request->route('id');


            if ($idRoute && $userToken != $idRoute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể truy cập tài nguyên này',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ hoặc đã hết hạn',
            ], 401);
        }

        return $next($request);
    }
}
