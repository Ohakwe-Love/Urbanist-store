<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_reference',
        'gateway',
        'currency',
        'authorization_url',
        'access_code',
        'method',
        'amount',
        'status',
        'paid_at',
        'verified_at',
        'disputed_at',
        'gateway_response',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'disputed_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
