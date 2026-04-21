<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'is_thumbnail',
        'sort_order',
    ];

    protected $casts = [
        'is_thumbnail' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getResolvedUrlAttribute(): string
    {
        if (Storage::disk('public')->exists($this->image_url)) {
            return asset('storage/'.$this->image_url);
        }

        if (file_exists(public_path('assets/images/'.$this->image_url))) {
            return asset('assets/images/'.$this->image_url);
        }

        return asset($this->image_url);
    }
}
