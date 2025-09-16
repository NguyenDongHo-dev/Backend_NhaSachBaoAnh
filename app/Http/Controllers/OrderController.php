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

    public function index(Request $request)
    {
        $user = JWTAuth::user();

        $limit = $request->input("limit", 10);

        $orders = Order::with(['order_items'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);


        return response()->json([
            'success' => true,
            'message' => 'Lay tat ca hoa don hanh cong',
            'data' => OrderResource::collection($orders),
            'total' => $orders->total(),
            'limit' => $orders->perPage(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    public function store(OrderRequest $request)
    {

        $user = JWTAuth::user();

        $order_items = $request->input('products');

        $dataOrder = $request->only(["shipping_address", "recipient_phone", "order_recipient_name", 'price_shipping', "delivery_method", 'notes']);
        $dataOrder['user_id'] = $user->id;

        $totalPrice = 0;

        foreach ($order_items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalPrice += $product->price * $item['quantity'];
        }

        $dataOrder['total_price'] = $totalPrice;
        $dataOrder['total_all'] = $totalPrice + $dataOrder['price_shipping'];


        $order = Order::create($dataOrder);

        foreach ($order_items as $item) {
            $product = Product::findOrFail($item['product_id']);
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



    public function update(OrderRequest $request, $id)
    {
        $dataOrder = $request->only([
            "status",
            "shipping_address",
            "recipient_phone",
            "order_recipient_name",
            "price_shipping",
            "delivery_method",
            "notes",
            "paid",
        ]);

        $totalPrice = 0;
        $order_items = $request->input('products', []);

        $order = Order::with('order_items.product.image')
            ->where('id', $id)
            ->firstOrFail();

        if (!empty($order_items)) {
            $isSuccess = $dataOrder['status'] === "success";

            $order->order_items()
                ->whereNotIn('product_id', collect($order_items)->pluck('product_id'))
                ->delete();

            $currentItems = $order->order_items;

            foreach ($order_items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($isSuccess && $order->getOriginal('status') !== 'success') {
                    if ($product['stock'] < $item['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Sản phẩm {$product->name} hết hàng",
                        ]);
                    }
                    $product->update([
                        'sold' => $item['quantity'] + $product['sold'],
                        'stock' => $product['stock'] - $item['quantity']
                    ]);
                }
                $totalPrice += $product->price * $item['quantity'];
                $orderItem = $currentItems->firstWhere('product_id', $item['product_id']);

                if ($orderItem) {
                    $orderItem->update([
                        'quantity' => $item['quantity'],
                        'price'    => $product->price,
                    ]);
                }
            }
        }

        $dataOrder['total_price'] = $totalPrice;
        $dataOrder['total_all'] = $totalPrice + $dataOrder['price_shipping'];

        if ($dataOrder['paid'] === 1) {
            $dataOrder['paid_at'] = now();
        }

        $order->update($dataOrder);

        $order->load('order_items.product.image');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data'    => new OrderResource($order),
        ]);
    }


    public function detailOrder($id)
    {
        $user = JWTAuth::user();

        $data = Order::with('order_items.product.image')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'lay chi tiet hoa dan thanh cong',
            'data' => new OrderResource($data),
        ]);
    }



    public function updateToCancelled($id)
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

        $order->update(['status' => 'cancel']);

        return response()->json([
            'success' => true,
            'message' => 'Huy dơn hang thanh cong',
        ]);
    }
    //admin
    public function allOrder(Request $request)
    {
        $limit = $request->input("limit", 10);
        $status = $request->input('status', "all");
        $typeSearch = $request->input("typeSearch", "order_recipient_name");
        $search = $request->input("search", "");


        $allowedSearchColumns = ['order_number', 'order_recipient_name', 'recipient_phone'];
        $allowedStatuses = ['padding', 'shipping', 'success', 'cancel'];


        if (!in_array($typeSearch, $allowedSearchColumns) && $typeSearch !== 'all') {
            $typeSearch = 'order_recipient_name';
        }

        $query = Order::with(['order_items.product.image']);

        if ($status !== 'all' && in_array($status, $allowedStatuses)) {
            $query->where('status', $status);
        }

        if ($search !== "") {
            if ($typeSearch === "all") {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('order_recipient_name', 'like', "%{$search}%")
                        ->orWhere('recipient_phone', 'like', "%{$search}%");
                });
            } else {
                $query->where($typeSearch, 'like', "%{$search}%");
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Lay tat ca hoa don hanh cong',
            'data' => OrderResource::collection($orders),
            'total' => $orders->total(),
            'limit' => $orders->perPage(),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ]);
    }

    public function detailsByAdnin($id)
    {
        $data = Order::with('order_items.product.image')
            ->where('id', $id)->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'lay chi tiet hoa dan thanh cong tu admin thanh cong',
            'data' => new OrderResource($data),
        ]);
    }


    public function cancelled($id)
    {
        $order = Order::where('id', $id)
            ->firstOrFail();

        $order->update(['status' => 'cancel']);

        return response()->json([
            'success' => true,
            'message' => 'Hủy đơn hàng thành công',
        ]);
    }
}
