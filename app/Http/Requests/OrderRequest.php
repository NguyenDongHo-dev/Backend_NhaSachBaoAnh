<?php

namespace App\Http\Requests;


class OrderRequest extends BaseRequest
{

    public function rules()
    {
        $orderId = $this->route("id");


        if ($this->isMethod('post')) {
            return [
                'payment_method' => 'required|string',
                'products' => 'required|array',

            ];
        }


        if ($this->isMethod('put') && $orderId) {
            return [
                'payment_method' => 'sometimes|string',
                'notes' => 'sometimes|string',
                'status' => 'sometimes|string',
                'paid_at' => 'nullable|date_format:Y-m-d'
            ];
        }
        return [];
    }
}
