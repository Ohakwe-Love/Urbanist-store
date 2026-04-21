<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = require database_path('seeders/data/products.php');

        Product::query()
            ->where('title', 'Diamond Halo Stud Sociis')
            ->delete();

        foreach ($products as $product) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($product['category'])],
                [
                    'name' => $product['category'],
                    'description' => 'Curated ' . strtolower($product['category']) . ' pieces selected for the Urbanist catalog.',
                    'is_active' => true,
                ]
            );

            $productModel = Product::withTrashed()->updateOrCreate(
                ['slug' => $product['slug'] ?? Str::slug($product['title'])],
                [
                    'title' => $product['title'],
                    'description' => $product['description'],
                    'image_url' => $product['image_url'],
                    'price' => $product['price'],
                    'sale_price' => $product['sale_price'] ?? null,
                    'discount' => $product['discount'] ?? null,
                    'stock_quantity' => $product['stock_quantity'] ?? 0,
                    'category_id' => $category->id,
                    'category' => $category->name,
                    'size' => $product['size'] ?? null,
                    'is_new' => $product['is_new'] ?? false,
                    'is_featured' => $product['is_featured'] ?? false,
                ]
            );

            if ($productModel->trashed()) {
                $productModel->restore();
            }

            $tagIds = collect($product['tags'] ?? [])
                ->map(function (string $tagName) {
                    return Tag::updateOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    )->id;
                })
                ->all();

            $productModel->tags()->sync($tagIds);
        }
    }
}
