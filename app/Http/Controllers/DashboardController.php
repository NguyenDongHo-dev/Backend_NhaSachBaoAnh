<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $status = $request->input("status", "all");

        $totalUser = User::where('role', 0)->count();

        $ordersQuery = Order::query();

        if ($status !== "all") {
            $ordersQuery->where("status", $status);
        }

        $totalPrice = $ordersQuery->sum('total_price');

        $totalOrders = $ordersQuery->count();

        $ordersPerMonth = $ordersQuery->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->orderBy('created_at', 'asc')
            ->get();

        $ordersPerDay = $ordersQuery->select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->orderBy('created_at', 'asc')
            ->get();

        $orderNew = Order::select('id', 'order_number', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $productLowStock = Product::select('id', 'name', 'stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();


        $statusCounts = Order::select(
            'status',
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Lay dashboard thanh cong",
            'data' => [
                'totalUser' => $totalUser,
                'totalOrders' => $totalOrders,
                'orderNew' => $orderNew,
                'productLowStock' => $productLowStock,
                'totalPrice' => $totalPrice,
                'ordersPerMonth' => $ordersPerMonth,
                'ordersPerDay' => $ordersPerDay,
                'statusCounts' => $statusCounts

            ]

        ]);
    }
}
