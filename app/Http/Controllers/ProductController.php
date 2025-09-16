<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use App\Models\Product_image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $sort  = $request->input('sort', 'latest');
        $status = $request->input('status', "block");
        $limit = $request->input("limit", 10);
        $categoryId = $request->input("categoryId");
        $searchName = $request->input('name');


        $query = Product::with('image', 'category');




        if (!empty($searchName)) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }
        // Lọc sản phẩm theo trạng thái: block = hiển thị, hidden = ẩn, all = tất cả
        if ($status !== "all") {
            $query->where('status', $status === "block" ? 1 : 0);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'discount':
                $query->orderBy('discount', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Lấy sản phẩm thành công',
            'data' => ProductResource::collection($products),
            'total' => $products->total(),
            'limit' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),

        ]);
    }



    public function store(ProductRequest $request)
    {
        // $data = json_decode($request->input('data'), true);
        $data = $request->only(['name', 'description', 'short_description', 'price', 'status', 'stock', 'category_id', 'discount']);

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



    public function show($slug)
    {
        $product = Product::with('image', 'category', 'reviews')->withCount(['reviews as total_reviews' => function ($q) {
            $q->where('status', 1);
        }])
            ->withAvg(['reviews as rating' => function ($q) {
                $q->where('status', 1);
            }], 'rating')->where("slug", $slug)->first();

        return response()->json([
            'success' => true,
            'message' => "lay chi tiet san pham thanh cong",
            'data' => new ProductResource($product),
        ]);
    }



    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'description', 'price', 'status', 'stock', 'category_id', 'discount']);

        $oldImages = $request->input('old_images', []);

        foreach ($product->image as $oldImage) {
            if (!in_array($oldImage->id, $oldImages)) {
                // xoá file vật lý (nếu cần)
                if (Storage::exists(str_replace('storage/', 'public/', $oldImage->url))) {
                    Storage::delete(str_replace('storage/', 'public/', $oldImage->url));
                }
                $oldImage->delete();
            }
        }

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
