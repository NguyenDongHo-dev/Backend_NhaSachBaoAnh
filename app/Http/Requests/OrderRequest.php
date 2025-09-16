<?php

namespace App\Http\Requests;


class OrderRequest extends BaseRequest
{

    public function rules()
    {
        $orderId = $this->route("id");


        if ($this->isMethod('post')) {
            return [
                'products' => 'required|array',
                'shipping_address' => 'required|string',
                'recipient_phone' => 'required|string',
                'order_recipient_name' => 'required|string',
                'price_shipping' => 'required|string',
                'delivery_method' => 'required|string',
            ];
        }

        // if ($this->isMethod('put') && $orderId) {
        //     return [
        //         'notes' => 'sometimes|string',
        //         'status' => 'sometimes|string',
        //         'paid_at' => 'nullable|date_format:Y-m-d'
        //     ];
        // }
        return [];
    }
}
