<?php

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

function checkoutProduct(array $attributes = []): Product
{
    static $sequence = 1;

    $number = $sequence++;

    return Product::create(array_merge([
        'title' => "Checkout Product {$number}",
        'slug' => "checkout-product-{$number}",
        'description' => 'Product used for checkout tests.',
        'price' => 249.99,
        'category' => 'Living Room',
        'stock_quantity' => 8,
    ], $attributes));
}

function checkoutPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'buyer@example.com',
        'phone' => '+2348012345678',
        'first_name' => 'Ada',
        'last_name' => 'Nwosu',
        'address' => '14 Prince Street',
        'address_2' => 'Suite 4B',
        'city' => 'Lagos',
        'state' => 'Lagos',
        'postal_code' => '100001',
        'country' => 'Nigeria',
        'save_address' => 1,
        'order_notes' => 'Please call on arrival.',
    ], $overrides);
}

it('initializes a paystack checkout and creates a pending order', function () {
    config([
        'services.paystack.secret_key' => 'sk_test_mock',
        'services.paystack.public_key' => 'pk_test_mock',
        'services.paystack.base_url' => 'https://api.paystack.co',
        'services.paystack.currency' => 'USD',
    ]);

    Http::fake([
        'https://api.paystack.co/transaction/initialize' => Http::response([
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/mock-authorize',
                'access_code' => 'ACCESS_123',
            ],
        ], 200),
    ]);

    $user = User::factory()->create([
        'email' => 'buyer@example.com',
    ]);

    $product = checkoutProduct([
        'price' => 300.00,
        'stock_quantity' => 5,
    ]);

    $cart = $user->cart()->create();
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 300.00,
    ]);

    $response = $this->actingAs($user)->post(route('checkout.store'), checkoutPayload());

    $response->assertRedirect('https://checkout.paystack.com/mock-authorize');

    $order = Order::first();
    expect($order)->not->toBeNull();
    expect($order->status)->toBe('pending');
    expect($order->payment_status)->toBe('pending');
    expect($order->total)->toBe('645.00');

    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'gateway' => 'paystack',
        'method' => 'paystack',
        'status' => 'pending',
        'authorization_url' => 'https://checkout.paystack.com/mock-authorize',
    ]);
});

it('requires login before checkout and returns the customer there after login', function () {
    $user = User::factory()->create([
        'email' => 'checkout-login@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->get(route('checkout'))
        ->assertRedirect(route('login'));

    $this->post(route('login.authenticate'), [
        'login' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('checkout'));
});

it('verifies a successful paystack callback and finalizes the order', function () {
    config([
        'services.paystack.secret_key' => 'sk_test_mock',
        'services.paystack.public_key' => 'pk_test_mock',
        'services.paystack.base_url' => 'https://api.paystack.co',
        'services.paystack.currency' => 'USD',
    ]);

    Http::fake([
        'https://api.paystack.co/transaction/verify/*' => Http::response([
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'status' => 'success',
                'amount' => 64500,
                'reference' => 'PAY-URB-MOCK1234',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $product = checkoutProduct([
        'price' => 300.00,
        'stock_quantity' => 5,
    ]);

    $cart = $user->cart()->create();
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 300.00,
    ]);

    $order = Order::create([
        'order_number' => 'URB-20260424-MOCK',
        'user_id' => $user->id,
        'status' => 'pending',
        'payment_status' => 'pending',
        'fulfillment_status' => 'pending',
        'subtotal' => 600.00,
        'shipping_fee' => 45.00,
        'total' => 645.00,
        'currency' => 'USD',
        'email' => $user->email,
        'phone' => '+2348012345678',
        'shipping_name' => 'Ada Nwosu',
        'shipping_address' => '14 Prince Street',
        'shipping_city' => 'Lagos',
        'shipping_state' => 'Lagos',
        'shipping_postal_code' => '100001',
        'shipping_country' => 'Nigeria',
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'quantity' => 2,
        'unit_price' => 300.00,
        'total_price' => 600.00,
        'attributes' => ['category' => $product->category],
    ]);

    $order->shipment()->create([
        'status' => 'pending',
        'shipping_fee' => 45.00,
    ]);

    $payment = Payment::create([
        'order_id' => $order->id,
        'payment_reference' => 'PAY-URB-MOCK1234',
        'gateway' => 'paystack',
        'currency' => 'USD',
        'method' => 'paystack',
        'amount' => 645.00,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('checkout.callback', [
        'reference' => $payment->payment_reference,
    ]));

    $response->assertRedirect(route('checkout.complete', $order));

    expect($payment->fresh()->status)->toBe('successful');
    expect($order->fresh()->status)->toBe('confirmed');
    expect($order->fresh()->payment_status)->toBe('paid');
    expect($product->fresh()->stock_quantity)->toBe(3);
    expect($cart->fresh()->items()->count())->toBe(0);
});
