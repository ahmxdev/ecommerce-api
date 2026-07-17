<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'final_price',
    'discount_amount',
    'status',
    'coupon_id',
    'coupon_code',
    'shipping_country',
    'shipping_state',
    'shipping_city',
    'shipping_district',
    'shipping_street',
    'shipping_building',
    'shipping_floor',
    'shipping_apartment',
    'shipping_landmark',
])]

class Order extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
