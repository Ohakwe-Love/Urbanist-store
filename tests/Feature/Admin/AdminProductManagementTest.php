<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;

it('allows admins to create a product with category and tags', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::create([
        'name' => 'Living Room',
        'slug' => 'living-room',
        'is_active' => true,
    ]);
    $tag = Tag::create([
        'name' => 'Featured Drop',
        'slug' => 'featured-drop',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'title' => 'Cloud Sofa',
        'slug' => 'cloud-sofa',
        'description' => 'Deep-seat sofa for modern living rooms.',
        'price' => 899.99,
        'sale_price' => 799.99,
        'discount' => 11,
        'stock_quantity' => 7,
        'size' => 'large',
        'category_id' => $category->id,
        'tags' => [$tag->id],
        'is_new' => 1,
        'is_featured' => 1,
        'colors' => 'Ivory, Sand',
        'materials' => 'Oak, boucle fabric',
        'specifications' => "Dimensions: 220cm x 98cm\nWarranty: 2 years",
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $product = Product::where('slug', 'cloud-sofa')->first();

    expect($product)->not->toBeNull();
    expect($product->category_id)->toBe($category->id);
    expect($product->is_featured)->toBeTrue();
    expect($product->tags()->pluck('tags.id')->all())->toContain($tag->id);
    expect($product->specifications()->count())->toBe(4);
});
