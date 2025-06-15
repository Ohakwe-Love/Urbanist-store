<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSpecification;

class ProductSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Product::all() as $product) {
            ProductSpecification::create([
                'product_id' => $product->id,
                'label'      => 'Dimensions',
                'value'      => 'W30" x D32" x H42"',
            ]);
            ProductSpecification::create([
                'product_id' => $product->id,
                'label'      => 'Weight',
                'value'      => '35 lbs',
            ]);
            ProductSpecification::create([
                'product_id' => $product->id,
                'label'      => 'Materials',
                'value'      => 'Solid hardwood, Premium polyester blend fabric',
            ]);
            ProductSpecification::create([
                'product_id' => $product->id,
                'label'      => 'Colors',
                'value'      => 'Null',
            ]);
            ProductSpecification::create([
                'product_id' => $product->id,
                'label'      => 'Origin',
                'value'      => 'Made in USA',
            ]);
        }
    }
}
