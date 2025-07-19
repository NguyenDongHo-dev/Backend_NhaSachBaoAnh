<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'required' => ':attribute không được để trống',
            'email' => ':attribute không đúng định dạng',
            'unique' => ':attribute đã tồn tại',
            'min' => ':attribute phải có ít nhất :min ký tự',
            'max' => ':attribute không được quá :max ký tự',
            'numeric' => ':attribute phải là số ',
            'string' => 'Phải là dạng chữ',
            'exists' => ':attribute không tồn tại'

        ];
    }

    public function attributes()
    {
        return [
            // Common
            'email' => "Email",
            'password' => "Mật khẩu",
            'name' => "Tên",

            //image
            'url' => "Image",


            // Foreign keys
            'product_id' => 'Sản phẩm',
            'image_id' => 'Hình ảnh',
            'category_id' => 'Danh mục',
            'product_id' => 'Sản phẩm',
            'user_id' => 'Người dùng',

            //order
            'total_price' => 'Tổng giá hóa đơn',
            'pait_at' => 'Thời gian thanh toán',
            'payment_method' => 'Phương thức thanh toán',

            //riview
            'comment' => "Đánh giá",
            'rating' => "Số sao",

            //product
            'price' => "Giá",

            //order-item
            'quantity' => 'Số lượng',

            //Product_variants
            'stock' => 'Số hàng'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => "Dữ liệu chưa hợp lệ",
            'errors' => $validator->errors()

        ], 422);
        throw new HttpResponseException($response);
    }
}
