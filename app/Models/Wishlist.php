<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id'
    ];

    // Relationship with User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Product model
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
