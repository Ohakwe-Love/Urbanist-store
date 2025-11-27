<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// class CartItem extends Model
// {
//     protected $fillable = [
//         'session_id',
//         'user_id',
//         'product_id',
//         'product_name',
//         'price',
//         'quantity',
//         'product_options'
//     ];

//     protected $casts = [
//         'product_options' => 'array',
//         'price' => 'decimal:2'
//     ];

//     public function product(): BelongsTo
//     {
//         return $this->belongsTo(Product::class);
//     }

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function getSubtotalAttribute(): float
//     {
//         return $this->price * $this->quantity;
//     }
// }


class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id',
     'quantity', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}