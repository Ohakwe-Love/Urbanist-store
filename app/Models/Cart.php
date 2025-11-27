<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Auth;


// class Cart extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'session_id',
//         'user_id',
//         'product_id',
//         'product_name',
//         'product_price',
//         'product_image',
//         'quantity',
//         'product_options'
//     ];

//     protected $casts = [
//         'product_options' => 'array',
//         'product_price' => 'decimal:2'
//     ];

//     // Relationships
//     public function product()
//     {
//         return $this->belongsTo(Product::class);
//     }

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }

//     // Scopes
//     public function scopeForCurrentUser($query)
//     {
//         return $query->where(function ($q) {
//             if (Auth::check()) {
//                 $q->where('user_id', Auth::id());
//             } else {
//                 $q->where('session_id', session()->getId());
//             }
//         });
//     }

//     // Helper methods
//     public static function getCartItems()
//     {
//         return self::forCurrentUser()->with('product')->get();
//     }

//     public static function getCartCount()
//     {
//         return self::forCurrentUser()->sum('quantity');
//     }

//     public static function getCartTotal()
//     {
//         return self::forCurrentUser()->get()->sum(function ($item) {
//             return $item->product_price * $item->quantity;
//         });
//     }

//     public function getSubtotalAttribute()
//     {
//         return $this->product_price * $this->quantity;
//     }

//     public static function mergeCarts($sessionId, $userId)
//     {
//         // Get session cart items
//         $sessionItems = self::where('session_id', $sessionId)
//                         ->whereNull('user_id')
//                         ->get();
        
//         foreach ($sessionItems as $sessionItem) {
//             // Check if user already has this item
//             $existingItem = self::where('user_id', $userId)
//                             ->where('product_id', $sessionItem->product_id)
//                             ->where('product_options', $sessionItem->product_options)
//                             ->first();
            
//             if ($existingItem) {
//                 // Merge quantities
//                 $existingItem->increment('quantity', $sessionItem->quantity);
//                 $sessionItem->delete();
//             } else {
//                 // Transfer to user
//                 $sessionItem->update(['user_id' => $userId]);
//             }
//         }
//     }
// } 


// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\HasMany;

// class Cart extends Model
// {
//     protected $fillable = ['user_id', 'session_id'];

//     public function user(): BelongsTo
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function items(): HasMany
//     {
//         return $this->hasMany(CartItem::class);
//     }

//     public function getTotalAttribute(): float
//     {
//         return $this->items->sum(function ($item) {
//             return $item->price * $item->quantity;
//         });
//     }

//     public function getItemCountAttribute(): int
//     {
//         return $this->items->sum('quantity');
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total from items
    public function getTotalAttribute(): float
    {
        return $this->items->sum('subtotal');
    }

    // Get item count
    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}