<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ProductRequest extends BaseRequest
{

    public function rules()
    {

        $productId = $this->route("id");

        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|unique:products',
                'price' => 'required|numeric|min:0',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category_id' => 'nullable|exists:categories,id',
                'stock' => 'required|numeric|min:0',
            ];
        }

        if ($this->isMethod('put') && $productId) {
            return [
                'name' => [
                    'sometimes',
                    'string',
                    Rule::unique('products', 'name')->ignore($productId, 'id'),
                ],
                'status' => 'sometimes|in:0,1',
                'price' => 'sometimes|numeric|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'stock' => 'sometimes|numeric|min:0',

            ];
        }


        return [];
    }
}
