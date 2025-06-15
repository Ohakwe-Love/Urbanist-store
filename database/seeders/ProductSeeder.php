<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = require database_path('seeders/data/products.php');

        foreach ($products as $product) {
            $baseSlug = Str::slug($product['title']);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug is unique
            while (DB::table('products')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            DB::table('products')->insert([
                'title'         => $product['title'],
                'slug'          => $slug,
                'description'   => $product['description'],
                'image_url'     => $product['image_url'],
                'price'         => $product['price'],
                'sale_price'    => $product['sale_price'] ?? null,
                'discount'      => $product['discount'] ?? null,
                'stock_quantity'=> isset($product['stock_quantity'])
                        ? $product['stock_quantity']
                        : (isset($product['stock_status']) && $product['stock_status'] === 'in_stock' ? 10 : 0),
                'category'      => $product['category'],
                'size'          => $product['size'] ?? null,
                'is_new'        => $product['is_new'] ?? false,
                'is_featured'   => $product['is_featured'] ?? false,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
