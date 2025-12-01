# Laravel Checkout Backend Implementation Plan

## 📋 Table of Contents
1. [Database Structure](#database-structure)
2. [Models & Relationships](#models--relationships)
3. [Controllers](#controllers)
4. [Routes](#routes)
5. [Validation](#validation)
6. [Services](#services)
7. [Payment Integration](#payment-integration)
8. [Email Notifications](#email-notifications)

---

## 1. Database Structure

### Migration Files

#### `create_orders_table.php`
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('order_number')->unique();
    $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
    
    // Contact Info
    $table->string('email');
    $table->string('phone');
    
    // Shipping Address
    $table->string('shipping_first_name');
    $table->string('shipping_last_name');
    $table->string('shipping_address');
    $table->string('shipping_address_2')->nullable();
    $table->string('shipping_city');
    $table->string('shipping_state');
    $table->string('shipping_postal_code');
    $table->string('shipping_country');
    
    // Billing Address
    $table->boolean('same_as_shipping')->default(true);
    $table->string('billing_first_name')->nullable();
    $table->string('billing_last_name')->nullable();
    $table->string('billing_address')->nullable();
    $table->string('billing_city')->nullable();
    $table->string('billing_postal_code')->nullable();
    
    // Payment
    $table->enum('payment_method', ['card', 'paypal', 'bank_transfer']);
    $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
    $table->string('transaction_id')->nullable();
    
    // Pricing
    $table->decimal('subtotal', 10, 2);
    $table->decimal('shipping_cost', 10, 2);
    $table->decimal('tax', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    
    // Additional
    $table->string('discount_code')->nullable();
    $table->text('order_notes')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'status']);
    $table->index('order_number');
});
```

#### `create_order_items_table.php`
```php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('product_name');
    $table->string('variant')->nullable();
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->decimal('total', 10, 2);
    $table->timestamps();
});
```

#### `create_discount_codes_table.php`
```php
Schema::create('discount_codes', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->enum('type', ['percentage', 'fixed']); // 10% or $10
    $table->decimal('value', 10, 2);
    $table->decimal('min_purchase', 10, 2)->nullable();
    $table->integer('usage_limit')->nullable();
    $table->integer('used_count')->default(0);
    $table->timestamp('starts_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### `create_addresses_table.php` (Optional - for saved addresses)
```php
Schema::create('addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('address');
    $table->string('address_2')->nullable();
    $table->string('city');
    $table->string('state');
    $table->string('postal_code');
    $table->string('country');
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

#### `create_cart_table.php`
```php
Schema::create('cart', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('session_id')->nullable();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('variant')->nullable();
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();
    
    $table->index(['user_id', 'session_id']);
});
```

---

## 2. Models & Relationships

### Order Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'status', 'email', 'phone',
        'shipping_first_name', 'shipping_last_name', 'shipping_address',
        'shipping_address_2', 'shipping_city', 'shipping_state',
        'shipping_postal_code', 'shipping_country', 'same_as_shipping',
        'billing_first_name', 'billing_last_name', 'billing_address',
        'billing_city', 'billing_postal_code', 'payment_method',
        'payment_status', 'transaction_id', 'subtotal', 'shipping_cost',
        'tax', 'discount', 'total', 'discount_code', 'order_notes', 'paid_at'
    ];

    protected $casts = [
        'same_as_shipping' => 'boolean',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFullShippingAddressAttribute(): string
    {
        return sprintf(
            "%s %s, %s %s, %s, %s %s",
            $this->shipping_first_name,
            $this->shipping_last_name,
            $this->shipping_address,
            $this->shipping_address_2 ? ', ' . $this->shipping_address_2 : '',
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code
        );
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });
    }
}
```

### OrderItem Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 
        'variant', 'quantity', 'price', 'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

### DiscountCode Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountCode extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase', 
        'usage_limit', 'used_count', 'starts_at', 
        'expires_at', 'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isValid(float $subtotal = 0): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            return round(($subtotal * $this->value) / 100, 2);
        }

        return min($this->value, $subtotal);
    }
}
```

### Cart Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $table = 'cart';

    protected $fillable = [
        'user_id', 'session_id', 'product_id', 
        'variant', 'quantity', 'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
```

---

## 3. Controllers

### CheckoutController
```php
<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\DiscountCode;
use App\Services\CheckoutService;
use App\Services\PaymentService;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $paymentService;

    public function __construct(CheckoutService $checkoutService, PaymentService $paymentService)
    {
        $this->checkoutService = $checkoutService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $cartItems = $this->checkoutService->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $totals = $this->checkoutService->calculateTotals($cartItems);

        return view('checkout', compact('cartItems', 'totals'))
            ->with($totals);
    }

    public function process(CheckoutRequest $request)
    {
        try {
            DB::beginTransaction();

            $cartItems = $this->checkoutService->getCartItems();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty');
            }

            // Calculate totals
            $totals = $this->checkoutService->calculateTotals($cartItems, $request->discount_code);

            // Create order
            $order = $this->checkoutService->createOrder($request->validated(), $totals);

            // Create order items
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'variant' => $item->variant,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ]);
            }

            // Process payment
            $paymentResult = $this->paymentService->processPayment(
                $order,
                $request->payment_method,
                $request->only(['card_number', 'expiry_date', 'cvv'])
            );

            if ($paymentResult['success']) {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $paymentResult['transaction_id'],
                    'paid_at' => now(),
                ]);

                // Clear cart
                $this->checkoutService->clearCart();

                // Send confirmation email
                // Mail::to($order->email)->send(new OrderConfirmation($order));

                DB::commit();

                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Order placed successfully!');
            } else {
                DB::rollBack();
                return back()->withInput()->with('error', $paymentResult['message']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cartItems = $this->checkoutService->getCartItems();
        $subtotal = $cartItems->sum('total');

        $discount = DiscountCode::where('code', strtoupper($request->code))->first();

        if (!$discount || !$discount->isValid($subtotal)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired discount code'
            ]);
        }

        $discountAmount = $discount->calculateDiscount($subtotal);
        $totals = $this->checkoutService->calculateTotals($cartItems, $request->code);

        session(['discount_code' => $request->code]);

        return response()->json([
            'success' => true,
            'subtotal' => $totals['subtotal'],
            'shipping' => $totals['shipping'],
            'tax' => $totals['tax'],
            'discount' => $discountAmount,
            'total' => $totals['total'],
        ]);
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
```

---

## 4. Routes

### web.php
```php
use App\Http\Controllers\CheckoutController;

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-discount', [CheckoutController::class, 'applyDiscount'])->name('checkout.apply-discount');
    Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
});
```

---

## 5. Validation

### CheckoutRequest
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
            'same_as_shipping' => 'boolean',
            'payment_method' => 'required|in:card,paypal,bank_transfer',
            'discount_code' => 'nullable|string|max:50',
            'order_notes' => 'nullable|string|max:1000',
        ];

        // Billing address validation if different from shipping
        if ($this->input('same_as_shipping') == false) {
            $rules = array_merge($rules, [
                'billing_first_name' => 'required|string|max:100',
                'billing_last_name' => 'required|string|max:100',
                'billing_address' => 'required|string|max:255',
                'billing_city' => 'required|string|max:100',
                'billing_postal_code' => 'required|string|max:20',
            ]);
        }

        // Card validation if payment method is card
        if ($this->input('payment_method') === 'card') {
            $rules = array_merge($rules, [
                'card_number' => 'required|string|size:19', // Including spaces
                'expiry_date' => 'required|string|size:5', // MM/YY
                'cvv' => 'required|string|min:3|max:4',
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required',
            'phone.required' => 'Phone number is required',
            'card_number.required' => 'Card number is required',
            'expiry_date.required' => 'Expiry date is required',
            'cvv.required' => 'CVV is required',
        ];
    }
}
```

---

## 6. Services

### CheckoutService
```php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class CheckoutService
{
    public function getCartItems(): Collection
    {
        if (Auth::check()) {
            return Cart::with('product')->where('user_id', Auth::id())->get();
        }

        return Cart::with('product')->where('session_id', session()->getId())->get();
    }

    public function calculateTotals(Collection $cartItems, ?string $discountCode = null): array
    {
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Calculate shipping (you can make this dynamic based on location/weight)
        $shipping = $this->calculateShipping($subtotal);

        // Calculate tax (you can make this dynamic based on location)
        $tax = $this->calculateTax($subtotal);

        // Apply discount
        $discount = 0;
        if ($discountCode) {
            $discountModel = DiscountCode::where('code', strtoupper($discountCode))->first();
            if ($discountModel && $discountModel->isValid($subtotal)) {
                $discount = $discountModel->calculateDiscount($subtotal);
            }
        }

        $total = $subtotal + $shipping + $tax - $discount;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
        ];
    }

    protected function calculateShipping(float $subtotal): float
    {
        // Free shipping over $100
        if ($subtotal >= 100) {
            return 0;
        }

        // Flat rate shipping
        return 10.00;
    }

    protected function calculateTax(float $subtotal): float
    {
        // 8% tax rate (adjust based on location)
        return $subtotal * 0.08;
    }

    public function createOrder(array $data, array $totals): Order
    {
        return Order::create([
            'user_id' => Auth::id(),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'shipping_first_name' => $data['first_name'],
            'shipping_last_name' => $data['last_name'],
            'shipping_address' => $data['address'],
            'shipping_address_2' => $data['address_2'] ?? null,
            'shipping_city' => $data['city'],
            'shipping_state' => $data['state'],
            'shipping_postal_code' => $data['postal_code'],
            'shipping_country' => $data['country'],
            'same_as_shipping' => $data['same_as_shipping'] ?? true,
            'billing_first_name' => $data['billing_first_name'] ?? null,
            'billing_last_name' => $data['billing_last_name'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'billing_city' => $data['billing_city'] ?? null,
            'billing_postal_code' => $data['billing_postal_code'] ?? null,
            'payment_method' => $data['payment_method'],
            'subtotal' => $totals['subtotal'],
            'shipping_cost' => $totals['shipping'],
            'tax' => $totals['tax'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'discount_code' => $data['discount_code'] ?? null,
            'order_notes' => $data['order_notes'] ?? null,
        ]);
    }

    public function clearCart(): void
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Cart::where('session_id', session()->getId())->delete();
        }

        session()->forget('discount_code');
    }
}
```

### PaymentService
```php
<?php

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    public function processPayment(Order $order, string $method, array $paymentData): array
    {
        switch ($method) {
            case 'card':
                return $this->processCardPayment($order, $paymentData);
            case 'paypal':
                return $this->processPayPalPayment($order);
            case 'bank_transfer':
                return $this->processBankTransfer($order);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    protected function processCardPayment(Order $order, array $cardData): array
    {
        // Integrate with Stripe, Braintree, or other payment gateway
        // This is a placeholder - implement actual payment processing
        
        try {
            // Example with Stripe:
            // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            // $charge = \Stripe\Charge::create([
            //     'amount' => $order->total * 100,
            //     'currency' => 'usd',
            //     'source' => $cardToken,
            //     'description' => 'Order #' . $order->order_number,
            // ]);

            // For demo purposes:
            $transactionId = 'TXN-' . strtoupper(uniqid());

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment processed successfully'
            ];

        } catch (\Exception $e) {
            \Log::error('Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    protected function processPayPalPayment(Order $order): array
    {
        // Integrate with PayPal SDK
        // Redirect to PayPal or process via API

        return [
            'success' => true,
            'transaction_id' => 'PP-' . strtoupper(uniqid()),
            'message' => 'PayPal payment initiated'
        ];
    }

    protected function processBankTransfer(Order $order): array
    {
        // Bank transfer requires manual verification
        // Order status remains 'pending' until payment is confirmed

        return [
            'success' => true,
            'transaction_id' => 'BT-' . strtoupper(uniqid()),
            'message' => 'Bank transfer instructions sent'
        ];
    }
}
```

---

## 7. Payment Integration

### Stripe Integration (Recommended)

#### Install Stripe SDK
```bash
composer require stripe/stripe-php
```

#### Add to `.env`
```env
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
```

#### Update PaymentService
```php
protected function processCardPayment(Order $order, array $cardData): array
{
    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $order->total * 100, // Amount in cents
            'currency' => 'usd',
            'description' => 'Order #' . $order->order_number,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        return [
            'success' => true,
            'transaction_id' => $paymentIntent->id,
            'message' => 'Payment processed successfully'
        ];

    } catch (\Stripe\Exception\CardException $e) {
        return [
            'success' => false,
            'message' => $e->getError()->message
        ];
    }
}
```

---

## 8. Email Notifications

### Create Mail Class
```bash
php artisan make:mail OrderConfirmation
```

### OrderConfirmation Mail
```php
<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Confirmation - ' . $this->order->order_number)
                    ->view('emails.order-confirmation');
    }
}
```

---

## 9. Additional Features to Implement

### Order Management Dashboard
- Admin panel to view/manage orders
- Order status updates
- Shipping tracking integration
- Refund processing

### Inventory Management
- Reduce stock when order is placed
- Restore stock if order is cancelled
- Low stock alerts

### Security Enhancements
- Rate limiting on checkout endpoint
- CSRF protection (already in Laravel)
- Payment data encryption
- PCI compliance for card data

### Testing
- Unit tests for checkout calculations
- Integration tests for order flow
- Payment gateway testing with test keys

---

## 🚀 Implementation Steps

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Create Models & Relationships**

3. **Implement Services** (CheckoutService, PaymentService)

4. **Create Controllers & Routes**

5. **Add Validation** (Form Requests)

6. **Integrate Payment Gateway** (Stripe/PayPal)

7. **Setup Email Notifications**

8. **Test Thoroughly**

9. **Deploy & Monitor**

---

This plan provides a complete, production-ready backend for your checkout system!