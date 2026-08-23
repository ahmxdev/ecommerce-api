<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'final_price' => $this->final_price,
            'discount_amount' => $this->discount_amount,
            'status' => $this->status,
            'coupon_code' => $this->coupon_code,
            'created_at' => $this->created_at,
        ];
    }
}
