<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;

class CartController extends Controller
{

    public function index($id)
    {
        $cart = Cart::with('product.image')->where('user_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay san pham trong cart thanh cong',
            'data' => CartResource::collection($cart),
        ]);
    }


    public function store(CartRequest $request, $id)
    {
        $data = $request->only(["product_id", 'quantity']);

        $cart = Cart::where('user_id', $id)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($cart) {
            $cart->quantity += $data['quantity'];
            $cart->save();
        } else {
            $data['user_id'] = $id;
            $cart = Cart::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Them thanh cong',
            'data' => $cart->load('product')
        ]);
    }


    public function destroy(CartRequest $request, $id)
    {
        $data = $request->only(["product_id"]);

        Cart::where('user_id', $id)->where('product_id', $data['product_id'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',
        ]);
    }
}
