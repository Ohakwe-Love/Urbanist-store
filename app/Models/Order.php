<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'fulfillment_status',
        'subtotal',
        'discount_total',
        'shipping_fee',
        'total',
        'currency',
        'email',
        'phone',
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'notes',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper((string) $this->currency)) {
            'NGN' => 'N',
            'GHS' => 'GH¢',
            'ZAR' => 'R',
            default => '$',
        };
    }
}
