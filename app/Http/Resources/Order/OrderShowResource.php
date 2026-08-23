<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShowResource extends JsonResource
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
            'shipping' => [
                'country' => $this->shipping_country,
                'state' => $this->shipping_state,
                'city' => $this->shipping_city,
                'district' => $this->shipping_district,
                'street' => $this->shipping_street,
                'building' => $this->shipping_building,
                'floor' => $this->shipping_floor,
                'apartment' => $this->shipping_apartment,
                'landmark' => $this->shipping_landmark,
            ],
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'name' => $item->item_name,
                'description' => $item->item_description,
                'price' => $item->item_price,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
