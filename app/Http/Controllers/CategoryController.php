<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaterogyRequest;
use App\Http\Resources\CetegoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'message' => 'Lay tat ca categories thanh cong',
            'data' => $categories,
        ]);
    }




    public function create(CaterogyRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'status' => $request->status,


        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tao category thanh cong',
            'data' => $category,
        ]);
    }


    public function store(Request $request)
    {
        //
    }

    public function type(Request $request, $slug)
    {
        $sort = $request->input('sort', "'latest'");
        $limit = $request->input("limit", 10);

        $category = Category::where('slug', $slug)->firstOrFail();;

        $productQuery = $category->product()->with("image");

        switch ($sort) {
            case 'price_asc':
                $productQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productQuery->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $productQuery->orderBy('created_at', 'desc');
                break;
        }

        $products = $productQuery->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Lay tat san pham thuoc type thanh cong',
            'data' => [
                'category' => new CetegoryResource($category),
                'products' => ProductResource::collection($products),

            ],
            'total' => $products->total(),
            'limit' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),

        ]);
    }


    public function show($slug)
    {
        $category = Category::where("slug", $slug)->firstOrFail();;


        return response()->json([
            'success' => true,
            'message' => "lay chi tiet category thanh cong",
            'data' => $category,
        ]);
    }



    public function update(CaterogyRequest $request, $id)
    {
        $data = $request->only(['name', 'status']);

        $category = Category::findOrFail($id);

        $category->update($data);

        return response()->json([
            "success" => true,
            'message' => 'Cap nhat thanh cong',
            'data' => $category,
        ]);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xoa thanh cong',
        ]);
    }
}
