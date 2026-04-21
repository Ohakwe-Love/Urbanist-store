<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class Product extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'title',
        'slug',
        'description',
        'discount',
        'price',
        'sale_price',
        'category_id',
        'category',
        'size',
        'stock_quantity',
        'image_url',
        'is_new',
        'is_featured'
    ];

    protected static function booted()
    {
        static::saving(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    // Scopes for filtering
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // public function scopeInStock($query)
    // {
    //     return $query->where('stock_quantity', '>', 0);
    // }

    // public function scopeSoldOut($query)
    // {
    //     return $query->where('stock_quantity', '<=', 0);
    // }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function isInWishlist()
    {
        if (!Auth::check()) {
            return false;
        }
        
        return $this->wishlists()
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isInStock($quantity = 1)
    {
        return $this->stock_quantity >= $quantity;
    }

    public function inCart()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->cartItems()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function getDisplayImageUrlAttribute(): string
    {
        if (!$this->image_url) {
            return asset('assets/images/new-arrivals/new-1.webp');
        }

        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        if (Storage::disk('public')->exists($this->image_url)) {
            return asset('storage/'.$this->image_url);
        }

        if (file_exists(public_path('assets/images/'.$this->image_url))) {
            return asset('assets/images/'.$this->image_url);
        }

        return asset($this->image_url);
    }
}
