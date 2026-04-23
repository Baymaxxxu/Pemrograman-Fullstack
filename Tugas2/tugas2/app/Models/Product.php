<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock'
    ];

    // relasi ke order_items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}