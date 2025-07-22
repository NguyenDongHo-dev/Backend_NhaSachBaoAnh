<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPSTORM_META\map;

class OrderController extends Controller
{

    public function index()
    {
        $user = JWTAuth::user();

        $orders = Order::with(['order_items', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay tat ca hoa don hanh cong',
            'data' => OrderResource::collection($orders),

        ]);
    }

    public function store(OrderRequest $request)
    {

        $user = JWTAuth::user();

        $dataUser = User::findOrFail($user->id);

        if ($dataUser->phone === null && $dataUser->address === null && $dataUser->phone === null) {
            return response()->json([
                'success' => true,
                'message' => 'Thong tin nguoi dung con thieu thong the dat hang (dia chi, so dien thoai)',

            ]);
        }


        $order_items = $request->input('products');

        $dataOrder = $request->only(['payment_method', 'notes', 'status']);
        $dataOrder['user_id'] = $user->id;

        $totalPrice = 0;

        foreach ($order_items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalPrice += $product->price * $item['quantity'];
        }

        $dataOrder['total_price'] = $totalPrice;


        $order = Order::create($dataOrder);

        foreach ($order_items as $item) {
            Order_item::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $product->price,

            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tao hoa don thanh cong',
            'order_id' => $order->id
        ]);
    }


    public function detailOrder($id)
    {
        $user = JWTAuth::user();

        $data = Order::with('user', 'order_items.product.image')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'lay chi tiet hoa dan thanh cong',
            'data' => new OrderResource($data),
        ]);
    }

    public function update(OrderRequest $request, $id)
    {

        $dataOrder = $request->only(['payment_method', 'note', 'status', 'paid_at']);

        $order = Order::with('user', 'order_items.product.image')
            ->where('id', $id)
            ->firstOrFail();

        $order->update($dataOrder);

        return response()->json([
            'success' => true,
            'message' => 'Cap nhat thanh cong',
            'data' => new OrderResource($order),
        ]);
    }

    public function destroy($id)
    {
        $user = JWTAuth::user();

        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->status === 'shipping') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đang được giao, không thể xoá!',
            ], 403);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',
        ]);
    }
    //admin
    public function allOrder()
    {
        $orders = Order::with(['order_items.product.image', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay thanh cong',
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function deteleAdmin($id)
    {
        $order = Order::where('id', $id)
            ->firstOrFail();

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',
        ]);
    }
}
