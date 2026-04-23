<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

function accountProduct(array $attributes = []): Product
{
    static $sequence = 1;

    $number = $sequence++;

    return Product::create(array_merge([
        'title' => "Account Product {$number}",
        'slug' => "account-product-{$number}",
        'description' => 'Product used for account feature tests.',
        'price' => 249.99,
        'sale_price' => 199.99,
        'category' => 'Living Room',
        'stock_quantity' => 4,
    ], $attributes));
}

it('sends a password reset link to a known user', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'resetme@example.com',
    ]);

    $response = $this->post(route('password.email'), [
        'email' => $user->email,
    ]);

    $response->assertSessionHas('success');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('redirects guests to the customer login form for customer-only pages', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('allows active customers to access customer-only pages', function () {
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Welcome back');
});

it('blocks inactive customers from customer-only pages', function () {
    $user = User::factory()->create([
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('does not treat an admin session as a customer session', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('allows a user to reset their password with a valid token', function () {
    $user = User::factory()->create([
        'email' => 'freshpass@example.com',
        'password' => bcrypt('old-password'),
    ]);

    $token = Password::broker('users')->createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertRedirect(route('login'));
    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('lets users save addresses in their address book', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('addresses.store'), [
        'label' => 'Home',
        'recipient_name' => 'Ada Nwosu',
        'phone' => '+1 (646) 555-0182',
        'address_line_1' => '14 Prince Street',
        'address_line_2' => 'Apt 5B',
        'city' => 'New York',
        'state' => 'NY',
        'postal_code' => '10012',
        'country' => 'USA',
        'is_default_shipping' => 1,
        'is_default_billing' => 1,
    ]);

    $response->assertRedirect(route('addresses.index'));

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'label' => 'Home',
        'city' => 'New York',
        'is_default_shipping' => true,
        'is_default_billing' => true,
    ]);
});

it('creates an address-book entry from the legacy profile address when needed', function () {
    $user = User::factory()->create([
        'name' => 'Ada Nwosu',
        'phone' => '+1 (646) 555-0182',
        'address' => '14 Prince Street',
        'city' => 'New York',
        'state' => 'NY',
        'postal_code' => '10012',
        'country' => 'USA',
    ]);

    $this->actingAs($user)
        ->get(route('addresses.index'))
        ->assertOk()
        ->assertSee('Primary Address');

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'label' => 'Primary Address',
        'address_line_1' => '14 Prince Street',
    ]);
});

it('shows a users order history and protects order details from other users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = accountProduct();

    $order = Order::create([
        'order_number' => 'URB-TEST-1001',
        'user_id' => $user->id,
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'fulfillment_status' => 'pending',
        'subtotal' => 199.99,
        'shipping_fee' => 15.00,
        'total' => 214.99,
        'currency' => 'USD',
        'email' => $user->email,
        'shipping_name' => $user->name,
        'shipping_address' => '14 Prince Street',
        'shipping_city' => 'New York',
        'shipping_state' => 'NY',
        'shipping_postal_code' => '10012',
        'shipping_country' => 'USA',
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'quantity' => 1,
        'unit_price' => 199.99,
        'total_price' => 199.99,
        'attributes' => ['category' => $product->category],
    ]);

    $this->actingAs($user)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertSee('URB-TEST-1001')
        ->assertSee($product->title);

    $this->actingAs($user)
        ->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee('URB-TEST-1001')
        ->assertSee($product->title);

    $this->actingAs($otherUser)
        ->get(route('orders.show', $order))
        ->assertNotFound();
});
