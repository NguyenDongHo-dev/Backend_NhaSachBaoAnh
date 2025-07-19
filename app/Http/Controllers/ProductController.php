<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use App\Models\Product_image;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $product = Product::with('image')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lay san pham thanh cong',
            'data' => ProductResource::collection($product),
        ]);
    }


    public function store(ProductRequest $request)
    {
        // $data = json_decode($request->input('data'), true);
        $data = $request->only(['name', 'description', 'price', 'status', 'stock', 'category_id']);

        // Tạo product
        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/images', $filename);

                Product_image::create([
                    'product_id' => $product->id,
                    'url' => 'storage/images/' . $filename,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo sản phẩm thành công',
            'data' => $product
        ]);
    }



    public function show($id)
    {
        $product = Product::with('image', 'category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => "lay chi tiet san pham thanh cong",
            'data' => new ProductResource($product),
        ]);
    }



    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->only(['name', 'description', 'price', 'status', 'stock', 'category_id']);

        if ($request->hasFile('images')) {
            foreach ($product->image as $oldImage) {
                $oldImage->delete();
            };

            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/images', $filename);

                Product_image::create([
                    'product_id' => $product->id,
                    'url' => 'storage/images/' . $filename,
                ]);
            }
        }

        $product->update($data);
        $product->load('image');

        return response()->json([
            'success' => true,
            'message' => 'cap nhat thanh cong',
            'data' => $product
        ]);
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoa san pham hanh cong',
        ]);
    }
}
