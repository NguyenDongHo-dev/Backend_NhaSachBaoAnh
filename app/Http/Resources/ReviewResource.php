<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'id'            => $this->id,
            'rating'        => $this->rating,
            'comment'       => $this->comment,
            'status'        => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'user' => optional($this->user)->email,
            'product' => optional($this->product)->name,

        ];
    }
}
