<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name'])]
class Brand extends Model
{
    use HasFactory;


    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
