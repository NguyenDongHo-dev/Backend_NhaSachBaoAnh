<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPSTORM_META\map;

class WishlistController extends Controller
{
    public function index()
    {
        $user = JWTAuth::user();
        $wishlist  = Wishlist::with('product.image')->where('user_id', $user->id)->get();

        $products = $wishlist->pluck('product');


        $IdProducts = $products->map(function ($product) {
            return $product->id;
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $IdProducts,
            'message' => 'Lay san pham wishlist thanh cong',
        ]);
    }


    public function store(WishlistRequest $request)
    {
        $productId = $request->input('product_id');

        $user = JWTAuth::user();

        $exits = Wishlist::where("user_id", $user->id)->where("product_id", $productId)->first();;

        if ($exits) {
            $exits->delete();
            return response()->json([
                'success' => true,
                'action' => "removed",
                'message' => "Xóa khỏi wishlist thành công",
            ]);
        }

        $wishlist = Wishlist::create([
            "user_id" => $user->id,
            "product_id" => $productId,
        ]);



        return response()->json([
            'success' => true,
            "action" => "add",
            'message' => "Tao wishlist thanh cong",
            'data' => $wishlist,
        ]);
    }

    public function productOfWishlist(Request $request)
    {
        $limit = $request->input("limit", 16);

        $user = JWTAuth::user();

        $products = Product::with('image', 'category')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('product_id')
                    ->from('wishlists')
                    ->where('user_id', $user->id);
            })
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => "lay san pham yeu thich thanh cong",
            'data' =>  ProductResource::collection($products),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
        ]);
    }


    public function destroy(WishlistRequest $request)
    {
        $productId = $request->input('product_id');
        $user = JWTAuth::user();

        Wishlist::where('user_id', $user->id)->where('product_id', $productId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',

        ]);
    }
}
