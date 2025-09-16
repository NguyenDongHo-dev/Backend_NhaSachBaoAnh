<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;



class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            $role = $payload->get('role');


            //1 is admin
            if ($role !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ban khong co quyen truy cap',

                ], 403);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token khong hop le hoac het hang admin',
                'role' => $role

            ], 401);
        }

        return $next($request);
    }
}
