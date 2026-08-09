<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'discount_percentage',
    'is_active',
    'expires_at'
])]



class Coupon extends Model
{
    use HasFactory;


    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
