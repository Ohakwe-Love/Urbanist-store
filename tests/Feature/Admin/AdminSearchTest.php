<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\ContentBlock;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\Tag;
use App\Models\User;

function searchableProduct(array $attributes = []): Product
{
    static $sequence = 1;

    $number = $sequence++;

    return Product::create(array_merge([
        'title' => "Searchable Product {$number}",
        'slug' => "searchable-product-{$number}",
        'description' => 'Product used for admin search tests.',
        'price' => 199.99,
        'category' => 'Living Room',
        'stock_quantity' => 8,
    ], $attributes));
}

it('lets admins search categories, tags, coupons, and content blocks', function () {
    $admin = Admin::factory()->create();

    Category::create([
        'name' => 'Outdoor Living',
        'slug' => 'outdoor-living',
        'description' => 'For patios and terraces.',
        'is_active' => true,
    ]);

    Tag::create([
        'name' => 'Summer Patio',
        'slug' => 'summer-patio',
    ]);

    Coupon::create([
        'code' => 'SUMMER15',
        'type' => 'percentage',
        'value' => 15,
        'is_active' => true,
    ]);

    ContentBlock::create([
        'key' => 'summer_campaign',
        'title' => 'Summer Campaign',
        'content' => 'Highlight seasonal outdoor pieces.',
        'is_active' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.categories.index', ['search' => 'Outdoor']))
        ->assertOk()
        ->assertSee('Outdoor Living')
        ->assertDontSee('Homepage banner');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.tags.index', ['search' => 'Patio']))
        ->assertOk()
        ->assertSee('Summer Patio');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.coupons.index', ['search' => 'SUMMER15']))
        ->assertOk()
        ->assertSee('SUMMER15');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.content.index', ['search' => 'campaign']))
        ->assertOk()
        ->assertSee('Summer Campaign')
        ->assertDontSee('Homepage banner');
});

it('lets admins search operational records', function () {
    $admin = Admin::factory()->create();
    $user = User::factory()->create([
        'name' => 'Amina Bello',
        'email' => 'amina@example.com',
    ]);
    $product = searchableProduct([
        'title' => 'Aster Lounge Chair',
        'slug' => 'aster-lounge-chair',
    ]);

    $order = Order::create([
        'order_number' => 'URB-OPS-2040',
        'user_id' => $user->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'fulfillment_status' => 'shipped',
        'subtotal' => 199.99,
        'shipping_fee' => 15.00,
        'total' => 214.99,
        'currency' => 'USD',
        'email' => $user->email,
        'shipping_name' => $user->name,
        'shipping_address' => '12 Marina Road',
        'shipping_city' => 'Lagos',
        'shipping_state' => 'LA',
        'shipping_postal_code' => '100001',
        'shipping_country' => 'Nigeria',
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'quantity' => 1,
        'unit_price' => 199.99,
        'total_price' => 199.99,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'payment_reference' => 'PAY-URB-2040',
        'method' => 'card',
        'amount' => 214.99,
        'status' => 'successful',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'carrier' => 'DHL',
        'tracking_number' => 'DHL-TRACK-2040',
        'status' => 'shipped',
        'shipping_fee' => 15.00,
    ]);

    Review::create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
        'title' => 'Excellent build',
        'body' => 'The chair looks incredible in our studio.',
        'is_approved' => true,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.orders.index', ['search' => 'URB-OPS-2040']))
        ->assertOk()
        ->assertSee('URB-OPS-2040')
        ->assertSee('Amina Bello');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.payments.index', ['search' => 'PAY-URB-2040']))
        ->assertOk()
        ->assertSee('PAY-URB-2040');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.shipments.index', ['search' => 'DHL-TRACK-2040']))
        ->assertOk()
        ->assertSee('DHL-TRACK-2040');

    $this->actingAs($admin, 'admin')
        ->get(route('admin.reviews.index', ['search' => 'Excellent build']))
        ->assertOk()
        ->assertSee('Excellent build')
        ->assertSee('Aster Lounge Chair');
});

it('paginates content blocks and preserves search queries', function () {
    $admin = Admin::factory()->create();

    foreach (range(1, 14) as $number) {
        ContentBlock::create([
            'key' => "content_block_{$number}",
            'title' => "Content Block {$number}",
            'content' => 'Searchable content block body.',
            'is_active' => true,
        ]);
    }

    $this->actingAs($admin, 'admin')
        ->get(route('admin.content.index', ['search' => 'Content Block']))
        ->assertOk()
        ->assertSee('admin/content?search=Content%20Block&amp;page=2', false);
});
