<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'price'
    ];

    // relasi ke order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // relasi ke product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}