<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
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
                    'message' => 'Ban thong the truy cap duoc',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token khong hop le hoac het hang',
            ], 401);
        }

        return $next($request);
    }
}
