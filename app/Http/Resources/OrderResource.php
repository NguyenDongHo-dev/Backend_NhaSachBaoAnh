<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'shipping_address' => $this->shipping_address,
            'recipient_phone' => $this->recipient_phone,
            'order_recipient_name' => $this->order_recipient_name,
            'delivery_method' => $this->delivery_method,
            'price_shipping' => $this->price_shipping,
            'total_all' => $this->total_all,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'notes' => $this->notes,
            'paid' => $this->paid,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_items' => $this->order_items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'stock' => $item->product->stock,
                        'image' => $item->product->image->pluck('url'),
                    ],
                ];
            }),
        ];
    }
}
