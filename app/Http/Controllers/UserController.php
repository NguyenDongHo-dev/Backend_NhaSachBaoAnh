<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function  index()
    {
        $users = User::all();
        return response()->json($users, 200);
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
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => "Login thanh cong",
            'token' => $token,
            'data' => $user,
        ], 200);
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
        );
    }

    public function detailsMe($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nguoi dung khong ton tai',
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Lay thanh cong',
            'data' => $user,
        ], 200);
    }

    public function details($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nguoi dung khong ton tai',
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Lay thanh cong',
            'data' => $user,
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
