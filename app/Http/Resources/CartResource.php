<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product_id'  => $this->product_id,
            'name'        => $this->product->name,
            'price'       => number_format($this->product->price, 0, ',', '.'),
            'quantity'    => $this->quantity,
            'total_price' => number_format($this->quantity * $this->product->price, 0, ',', '.'),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'image_url'   => $this->product->image->pluck('url'),
        ];
    }
}
