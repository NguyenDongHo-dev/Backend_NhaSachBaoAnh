<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTFactory;

class UserController extends Controller
{
    public function  index(Request $request)
    {
        $usersQuery = User::query();


        $sort = $request->input('sort', "latest");
        $limit = $request->input("limit", 20);
        $searchEmail = $request->input('email');


        if (!empty($searchEmail)) {
            $usersQuery->where('email', 'like', '%' . $searchEmail . '%');
        }

        switch ($sort) {
            case 'is_user':
                $usersQuery->where("role", 0);
                break;
            case 'is_admin':
                $usersQuery->where("role", 1);
                break;
            case 'latest':
            default:
                $usersQuery->orderBy('created_at', 'desc');
                break;
        }

        $user = $usersQuery->paginate($limit);


        return response()->json([
            'success' => true,
            'message' => 'Lấy tất cả người dùng thành công',
            'data' => $user->items(),
            'total' => $user->total(),
            'limit' => $user->perPage(),
            'current_page' => $user->currentPage(),
            'last_page' => $user->lastPage(),
        ]);
    }

    public function updateByAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);



        $data = $request->only(['name', 'email', 'phone', 'address', 'role']);
        $exists = User::where('email', $data['email'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email đã tồn tại cho người dùng khác'
            ], 422);
        }


        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => "Cap nhat nguoi dung thanh cong",
            'data' => $user
        ], 200);
    }

    public function detailsByAdmin($id)
    {

        $data = User::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Nguoi dung khong ton tai',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lay thanh cong',
            'data' => $data,
        ], 200);
    }

    public function register(UserRequest $request)
    {

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        return response()->json([
            'success' => true,
            'message' => "Dang ki thanh cong",
            'data' => $user
        ], 201);
    }

    public function createUser(UserRequest $request)
    {
        $data = $request->only(['name', 'email', 'password', 'address', 'role', 'phone']);

        $user = User::create(
            $data
        );

        return response()->json([
            'success' => true,
            'message' => "Tạo người dùng thành công",
            'data' => $user
        ], 201);
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => "Email khong duoc de trong",
            'email.email' => 'Email khong dung dinh dang',
            'password.required' => "Mat khau khong duoc de trong"

        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng',
            ], 201);
        }

        $token = JWTAuth::fromUser($user);

        $payload = JWTFactory::customClaims([
            'sub' => $user->id,
            'type' => 'refresh',
            'exp' => now()->addDays(30)->timestamp
        ])->make();


        $refreshToken = JWTAuth::encode($payload)->get();

        return response()->json([
            'success' => true,
            'message' => "Login thanh cong",
            'token' => $token,
            'refresh_Token' => $refreshToken,
            "data" => $user
        ], 200)->cookie(
            'refresh_token',
            $refreshToken,
            60 * 24 * 7,
            '/',
            'localhost',
            false,
            true,
            false,
            'Lax'
        );
    }


    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);


        $data = $request->only(['name', 'email', 'phone', 'address',]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => "Cap nhat thanh cong",
            'data' => $user
        ], 200);
    }


    public function logout()
    {
        auth()->logout();
        return response()->json(
            [
                'success' => true,
                "message" => 'Dang xuat thanh cong',
            ]
        )->cookie(
            'refresh_token',
            '',
            -1,
            '/',
            'localhost',
            false,
            true,
            false,
            'Lax'
        );
    }



    public function details()
    {
        $user = auth()->user();
        $data = User::find($user->id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Nguoi dung khong ton tai',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lay thanh cong',
            'data' => $data,
        ], 200);
    }

    public function delete($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nguoi dung khong ton tai',
            ]);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',
        ], 200);
    }
}
