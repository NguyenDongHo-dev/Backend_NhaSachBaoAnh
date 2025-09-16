<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReviewController extends Controller
{
    public function index(Request $request, $id)
    {
        // true =1
        $status = 1;
        $limit = $request->input("limit", 10);
        $q = Review::with('user')->where("product_id", $id)->where("status", $status)->orderBy('created_at', 'desc');
        $dataReview = $q->paginate($limit);

        return response()->json([
            "success" => true,
            "message" => 'Lay tat ca review trong san pham thanh cong',
            'data' => ReviewResource::collection($dataReview),
            'total' => $dataReview->total(),
            'limit' => $dataReview->perPage(),
            'current_page' => $dataReview->currentPage(),
            'last_page' => $dataReview->lastPage(),
        ]);
    }


    public function store(Request $request)
    {

        $user = JWTAuth::user();
        $data = $request->only(['product_id', 'rating', 'comment']);
        $data['user_id'] = $user->id;

        $isUserReview = Review::where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->exists();


        if ($isUserReview) {
            return response()->json([
                'success' => false,
                'message' => "Bạn đã đánh giá sản phẩm này rồi",
            ]);
        }

        $data = Review::create($data);

        return response()->json([
            'success' => true,
            'message' => "tao review thanh cong",
            'data' => new ReviewResource($data)

        ]);
    }

    public function update(Request $request, $id)
    {

        $user = JWTAuth::user();

        $data = $request->only(['rating', 'comment']);

        $dataReview = Review::where('user_id', $user->id)->where('product_id', $id)->first();;

        if (!$dataReview) {
            return response()->json([
                'success' => false,
                'message' => "Khong tiem thay danh gia",
            ]);
        }

        $dataReview->update($data);

        return response()->json([
            'success' => true,
            'message' => "tao update thanh cong",
        ]);
    }

    public function destroy($id)
    {
        $user = JWTAuth::user();

        Review::where('user_id', $user->id)->where('product_id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "xoa review thanh cong",
        ]);
    }


    //admin
    public function allReviews()
    {
        $data = Review::with('product', 'user')->get();

        return response()->json([
            'success' => true,
            'message' => "Lay tat ca review thanh cong",
            'data' => ReviewResource::collection($data)
        ]);
    }

    public function deleteReview($id)
    {
        $data = Review::findOrFail($id);

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => "Xoa review cua user thanh cong",
        ]);
    }
}
