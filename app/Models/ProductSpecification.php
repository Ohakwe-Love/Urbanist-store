<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $fillable = ['product_id', 'label', 'value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
