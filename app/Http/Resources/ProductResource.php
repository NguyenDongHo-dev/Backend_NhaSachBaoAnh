<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'                   => $this->id,
            'name'                 => $this->name,
            'description'          => $this->description,
            'short_description'    => $this->short_description,
            'discount'             => $this->discount,
            'price'          => $this->price,
            'slug'           => $this->slug,
            'stock'          => $this->stock,
            'sold'          => $this->sold,
            'status'         => $this->status,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'image'          => $this->image->map(function ($img) {
                return [
                    'id'  => $img->id,
                    'url' => $img->url,
                ];
            }),
            'total_reviews' => $this->total_reviews ?? 0,
            'rating'        => round($this->rating ?? 0, 1),
            'category' => [
                'name' => optional($this->category)->name,
                'slug' => optional($this->category)->slug,
                'id' => optional($this->category)->id,

            ],
        ];
    }
}
