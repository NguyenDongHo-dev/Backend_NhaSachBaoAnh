<?php

namespace App\Http\Requests;


class CartRequest extends BaseRequest
{

    public function rules()
    {

        if ($this->isMethod('post')) {
            return [
                'user_id' => 'nullable|exists:users,id',
                'product_id' => 'nullable|exists:products,id',
                'quantity' => 'required|numeric|min:0',
            ];
        }

        if ($this->isMethod('delete')) {
            return [
                'product_id' => 'required|exists:products,id',
            ];
        }

        return [];
    }
}
