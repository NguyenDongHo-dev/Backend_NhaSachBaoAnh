<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaterogyRequest;
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

    public function type($id)
    {
        $product = Category::with('product')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Lay tat san pham thuoc type thanh cong',
            'data' => $product,
        ]);
    }


    public function show($id)
    {
        $category = Category::findOrFail($id);


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
