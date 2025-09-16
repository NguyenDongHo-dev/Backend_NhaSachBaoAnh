<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class RefreshTokenController extends Controller
{
    public function refresh(Request $request)
    {
        // Lấy refresh token từ cookie
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'error' => 'Không tìm thấy refresh token trong cookie'
            ], 401);
        }

        try {
            // Decode refresh token bằng JWTAuth manager
            $payload = JWTAuth::manager()->decode(new Token($refreshToken));

            // Kiểm tra loại token
            if (!isset($payload['type']) || $payload['type'] !== 'refresh') {
                return response()->json([
                    'error' => 'Invalid token type'
                ], 400);
            }

            $userId = $payload['sub'] ?? null;

            if (!$userId) {
                return response()->json([
                    'error' => 'Token không chứa user id hợp lệ'
                ], 401);
            }

            $user = User::findOrFail($userId);

            // Sinh access token mới từ user
            $newAccessToken = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'token'   => $newAccessToken,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token không hợp lệ hoặc đã hết hạn',
                'msg'   => $e->getMessage(), // ⚡ log lỗi chi tiết để debug
            ], 401);
        }
    }
}
