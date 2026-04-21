<?php

use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;

function storefrontProduct(array $attributes = []): Product
{
    static $sequence = 1;

    $number = $sequence++;

    return Product::create(array_merge([
        'title' => "Storefront Product {$number}",
        'slug' => "storefront-product-{$number}",
        'description' => 'Storefront visibility test product.',
        'price' => 199.99,
        'sale_price' => 149.99,
        'category' => 'Living Room',
        'size' => 'medium',
        'stock_quantity' => 5,
    ], $attributes));
}

it('hides sold out products from home and shop pages', function () {
    $visibleProduct = storefrontProduct([
        'title' => 'Visible Sofa',
        'slug' => 'visible-sofa',
        'stock_quantity' => 3,
    ]);

    $soldOutProduct = storefrontProduct([
        'title' => 'Hidden Console',
        'slug' => 'hidden-console',
        'stock_quantity' => 0,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee($visibleProduct->title)
        ->assertDontSee($soldOutProduct->title);

    $this->get(route('shop'))
        ->assertOk()
        ->assertSee($visibleProduct->title)
        ->assertDontSee($soldOutProduct->title);
});

it('returns 404 for sold out product detail pages', function () {
    $soldOutProduct = storefrontProduct([
        'title' => 'Archived Desk',
        'slug' => 'archived-desk',
        'stock_quantity' => 0,
    ]);

    $this->get(route('show', $soldOutProduct->slug))->assertNotFound();
});

it('filters sold out products from customer wishlists and cart summaries', function () {
    $user = User::factory()->create();

    $visibleProduct = storefrontProduct([
        'title' => 'Wishlist Chair',
        'slug' => 'wishlist-chair',
        'stock_quantity' => 4,
    ]);

    $soldOutProduct = storefrontProduct([
        'title' => 'Wishlist Shelf',
        'slug' => 'wishlist-shelf',
        'stock_quantity' => 0,
    ]);

    Wishlist::create([
        'user_id' => $user->id,
        'product_id' => $visibleProduct->id,
    ]);

    Wishlist::create([
        'user_id' => $user->id,
        'product_id' => $soldOutProduct->id,
    ]);

    $cart = Cart::create([
        'user_id' => $user->id,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $visibleProduct->id,
        'quantity' => 1,
        'price' => $visibleProduct->price,
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $soldOutProduct->id,
        'quantity' => 1,
        'price' => $soldOutProduct->price,
    ]);

    $this->actingAs($user)
        ->get(route('wishlist'))
        ->assertOk()
        ->assertSee($visibleProduct->title)
        ->assertDontSee($soldOutProduct->title);

    $response = $this->actingAs($user)->getJson(route('cart.summary'));

    $response->assertOk();
    $response->assertJsonPath('success', true);
    expect($response->json('cart.product_ids'))->toBe([$visibleProduct->id]);
    expect($response->json('html'))->toContain($visibleProduct->title);
    expect($response->json('html'))->not->toContain($soldOutProduct->title);
});

it('keeps sold out products visible in the admin products index', function () {
    $admin = Admin::factory()->create();
    $soldOutProduct = storefrontProduct([
        'title' => 'Admin Only Ottoman',
        'slug' => 'admin-only-ottoman',
        'stock_quantity' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertSee($soldOutProduct->title);
});
