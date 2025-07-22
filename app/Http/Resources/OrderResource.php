<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'payment_method' => $this->payment_method,
            'total_price' => number_format($this->total_price, 0, ',', '.'),
            'status' => $this->status,
            'note' => $this->note,
            'paid_at ' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'address' => $this->user->address,
                'phone' => $this->user->phone,

            ],
            'order_items' => $this->order_items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'price' => number_format($item->price, 0, ',', '.'),
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image->pluck('url'),
                    ],
                ];
            }),
        ];
    }
}
