<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Wishlist;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class WishlistController extends Controller
{
    public function index($id)
    {

        $wishlist  = Wishlist::with('product.image')->where('user_id', $id)->get();

        $products = $wishlist->pluck('product');


        $formatted = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 0, ',', '.'),
                'image_url' => optional($product->image->first())->url,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'message' => 'Lay san pham wishlist thanh cong',
        ]);
    }


    public function store(WishlistRequest $request, $id)
    {
        $data = $request->only(['product_id']);


        $data['user_id'] = $id;
        $wishlist = Wishlist::create($data);

        return response()->json([
            'success' => true,
            'message' => "Tao wishlist thanh cong",
            'data' => $wishlist,
        ]);
    }


    public function destroy(WishlistRequest $request, $id)
    {
        $productId = $request->input('product_id');

        Wishlist::where('user_id', $id)->where('product_id', $productId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',

        ]);
    }
}
