<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\RegistrationEmailVerification;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\VerifyRegistrationEmail;
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

function registrationVerificationCodeFor(string $email): string
{
    $code = null;

    Notification::assertSentOnDemand(VerifyRegistrationEmail::class, function ($notification, array $channels, object $notifiable) use ($email, &$code) {
        if (($notifiable->routes['mail'] ?? null) !== $email) {
            return false;
        }

        $code = $notification->verificationCode();

        return in_array('mail', $channels, true);
    });

    expect($code)->not->toBeNull();

    return $code;
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

it('sends an email verification code before allowing registration to continue', function () {
    Notification::fake();

    $response = $this->post(route('register.email'), [
        'email' => 'newcustomer@example.com',
    ]);

    $response->assertRedirect(route('register.verify'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('registration_email_verifications', [
        'email' => 'newcustomer@example.com',
        'verified_at' => null,
    ]);

    expect(registrationVerificationCodeFor('newcustomer@example.com'))->toHaveLength(6);
});

it('does not allow a customer to finish registration before verifying their email', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Tobi Adebayo',
        'username' => 'tobiad',
        'email' => 'tobi@example.com',
        'password' => 'Str0ng!Pass',
        'password_confirmation' => 'Str0ng!Pass',
        'agreement' => 1,
    ]);

    $response->assertRedirect(route('register.verify'));
    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('users', [
        'email' => 'tobi@example.com',
    ]);
});

it('completes registration after the email address is verified', function () {
    Notification::fake();

    $this->post(route('register.email'), [
        'email' => 'verifiedcustomer@example.com',
    ])->assertRedirect(route('register.verify'));

    $this->post(route('register.verify-code'), [
        'email' => 'verifiedcustomer@example.com',
        'code' => registrationVerificationCodeFor('verifiedcustomer@example.com'),
    ])->assertRedirect(route('register.details'));

    $this->post(route('register.store'), [
        'name' => 'Verified Customer',
        'username' => 'verifiedcustomer',
        'email' => 'verifiedcustomer@example.com',
        'password' => 'Str0ng!Pass',
        'password_confirmation' => 'Str0ng!Pass',
        'agreement' => 1,
    ])->assertRedirect(route('login'));

    $user = User::where('email', 'verifiedcustomer@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->role)->toBe(User::ROLE_CUSTOMER);

    $this->assertDatabaseMissing('registration_email_verifications', [
        'email' => 'verifiedcustomer@example.com',
    ]);
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

it('blocks login for users whose email has not been verified', function () {
    $user = User::factory()->create([
        'email' => 'notverified@example.com',
        'password' => Hash::make('Str0ng!Pass'),
        'email_verified_at' => null,
    ]);

    $response = $this->post(route('login.authenticate'), [
        'login' => $user->email,
        'password' => 'Str0ng!Pass',
    ]);

    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

it('does not expose admin login links on customer auth pages', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee(route('admin.login'), false);

    $this->get(route('register'))
        ->assertOk()
        ->assertDontSee(route('admin.login'), false);
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
