# Modern Cart System for Laravel 12 E-commerce

This guide walks you through implementing a modern, AJAX-powered shopping cart system using Laravel 12, vanilla JavaScript, and iziToast notifications.

## 1. Database Setup

### Migration for Cart Items

```bash
php artisan make:migration create_cart_items_table
```

```php
// database/migrations/xxxx_xx_xx_create_cart_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->string('product_image')->nullable();
            $table->integer('quantity');
            $table->json('product_options')->nullable(); // For variants, size, color, etc.
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['session_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
```

## 2. Cart Model

```bash
php artisan make:model CartItem
```

```php
// app/Models/CartItem.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'product_name',
        'product_price',
        'product_image',
        'quantity',
        'product_options'
    ];

    protected $casts = [
        'product_options' => 'array',
        'product_price' => 'decimal:2'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAttribute()
    {
        return $this->product_price * $this->quantity;
    }
}
```

## 3. Cart Service

```bash
php artisan make:service CartService
```

```php
// app/Services/CartService.php
<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected function getIdentifier()
    {
        return Auth::check() 
            ? ['user_id' => Auth::id()]
            : ['session_id' => Session::getId()];
    }

    public function add($productId, $quantity = 1, $options = [])
    {
        $product = Product::findOrFail($productId);
        $identifier = $this->getIdentifier();

        // Check if item already exists in cart
        $cartItem = CartItem::where($identifier)
            ->where('product_id', $productId)
            ->where('product_options', json_encode($options))
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            return $cartItem;
        }

        return CartItem::create(array_merge($identifier, [
            'product_id' => $productId,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'product_image' => $product->image,
            'quantity' => $quantity,
            'product_options' => $options
        ]));
    }

    public function update($cartItemId, $quantity)
    {
        $cartItem = CartItem::where($this->getIdentifier())
            ->findOrFail($cartItemId);

        if ($quantity <= 0) {
            return $this->remove($cartItemId);
        }

        $cartItem->update(['quantity' => $quantity]);
        return $cartItem;
    }

    public function remove($cartItemId)
    {
        return CartItem::where($this->getIdentifier())
            ->findOrFail($cartItemId)
            ->delete();
    }

    public function clear()
    {
        return CartItem::where($this->getIdentifier())->delete();
    }

    public function getItems()
    {
        return CartItem::where($this->getIdentifier())
            ->with('product')
            ->get();
    }

    public function getCount()
    {
        return CartItem::where($this->getIdentifier())->sum('quantity');
    }

    public function getTotal()
    {
        return CartItem::where($this->getIdentifier())
            ->get()
            ->sum(function ($item) {
                return $item->product_price * $item->quantity;
            });
    }

    public function mergeGuestCart($sessionId)
    {
        if (!Auth::check()) return;

        $guestItems = CartItem::where('session_id', $sessionId)->get();
        
        foreach ($guestItems as $guestItem) {
            $existingItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $guestItem->product_id)
                ->where('product_options', json_encode($guestItem->product_options))
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
            }
        }
    }
}
```

## 4. Cart Controller

```bash
php artisan make:controller CartController
```

```php
// app/Http/Controllers/CartController.php
<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
        $count = $this->cartService->getCount();

        return view('cart.index', compact('items', 'total', 'count'));
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'array'
        ]);

        try {
            $cartItem = $this->cartService->add(
                $request->product_id,
                $request->quantity,
                $request->options ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $this->cartService->getCount(),
                'cart_total' => number_format($this->cartService->getTotal(), 2),
                'item' => $cartItem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            if ($request->quantity == 0) {
                $this->cartService->remove($id);
                $message = 'Item removed from cart';
            } else {
                $this->cartService->update($id, $request->quantity);
                $message = 'Cart updated successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $this->cartService->getCount(),
                'cart_total' => number_format($this->cartService->getTotal(), 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    public function remove($id): JsonResponse
    {
        try {
            $this->cartService->remove($id);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->cartService->getCount(),
                'cart_total' => number_format($this->cartService->getTotal(), 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item'
            ], 500);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clear();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_count' => 0,
                'cart_total' => '0.00'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    public function getCartData(): JsonResponse
    {
        return response()->json([
            'items' => $this->cartService->getItems(),
            'count' => $this->cartService->getCount(),
            'total' => number_format($this->cartService->getTotal(), 2)
        ]);
    }
}
```

## 5. Routes

```php
// routes/web.php
use App\Http\Controllers\CartController;

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
});
```

## 6. JavaScript Cart Manager

Create a new JavaScript file for managing cart operations:

```javascript
// public/js/cart-manager.js
class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.cartCountElement = document.querySelector('.cart-count');
        this.cartTotalElement = document.querySelector('.cart-total');
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartDisplay();
    }

    bindEvents() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn')) {
                e.preventDefault();
                this.addToCart(e.target);
            }
        });

        // Update quantity buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quantity-increase')) {
                e.preventDefault();
                this.updateQuantity(e.target, 1);
            } else if (e.target.matches('.quantity-decrease')) {
                e.preventDefault();
                this.updateQuantity(e.target, -1);
            }
        });

        // Remove item buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-item-btn')) {
                e.preventDefault();
                this.removeItem(e.target);
            }
        });

        // Clear cart button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.clear-cart-btn')) {
                e.preventDefault();
                this.clearCart();
            }
        });

        // Quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.quantity-input')) {
                this.updateCartItemQuantity(e.target);
            }
        });
    }

    async addToCart(button) {
        const productId = button.dataset.productId;
        const quantity = parseInt(button.dataset.quantity || 1);
        const form = button.closest('form');
        
        // Get product options if form exists
        let options = {};
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                if (key !== '_token' && key !== 'product_id' && key !== 'quantity') {
                    options[key] = value;
                }
            }
        }

        button.disabled = true;
        button.textContent = 'Adding...';

        try {
            const response = await this.makeRequest('/cart/add', 'POST', {
                product_id: productId,
                quantity: quantity,
                options: options
            });

            if (response.success) {
                this.showNotification('success', response.message);
                this.updateCartDisplay(response.cart_count, response.cart_total);
                this.animateCartIcon();
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            this.showNotification('error', 'Failed to add item to cart');
        } finally {
            button.disabled = false;
            button.textContent = 'Add to Cart';
        }
    }

    async updateQuantity(button, change) {
        const cartItemId = button.dataset.cartItemId;
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        const currentQuantity = parseInt(quantityInput.value);
        const newQuantity = Math.max(0, currentQuantity + change);

        await this.updateCartItemQuantity(null, cartItemId, newQuantity);
    }

    async updateCartItemQuantity(input, cartItemId = null, newQuantity = null) {
        if (input) {
            cartItemId = input.dataset.cartItemId;
            newQuantity = parseInt(input.value);
        }

        if (newQuantity < 0) newQuantity = 0;

        try {
            const response = await this.makeRequest(`/cart/${cartItemId}`, 'PATCH', {
                quantity: newQuantity
            });

            if (response.success) {
                this.showNotification('success', response.message);
                this.updateCartDisplay(response.cart_count, response.cart_total);
                
                if (newQuantity === 0) {
                    this.removeCartItemFromDOM(cartItemId);
                } else if (input) {
                    input.value = newQuantity;
                    this.updateItemTotal(cartItemId, newQuantity);
                }
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            this.showNotification('error', 'Failed to update cart');
        }
    }

    async removeItem(button) {
        const cartItemId = button.dataset.cartItemId;

        if (!confirm('Are you sure you want to remove this item?')) {
            return;
        }

        try {
            const response = await this.makeRequest(`/cart/${cartItemId}`, 'DELETE');

            if (response.success) {
                this.showNotification('success', response.message);
                this.updateCartDisplay(response.cart_count, response.cart_total);
                this.removeCartItemFromDOM(cartItemId);
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            this.showNotification('error', 'Failed to remove item');
        }
    }

    async clearCart() {
        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }

        try {
            const response = await this.makeRequest('/cart', 'DELETE');

            if (response.success) {
                this.showNotification('success', response.message);
                this.updateCartDisplay(0, '0.00');
                this.clearCartDOM();
            } else {
                this.showNotification('error', response.message);
            }
        } catch (error) {
            this.showNotification('error', 'Failed to clear cart');
        }
    }

    async updateCartDisplay(count = null, total = null) {
        if (count === null || total === null) {
            try {
                const response = await this.makeRequest('/cart/data', 'GET');
                count = response.count;
                total = response.total;
            } catch (error) {
                console.error('Failed to fetch cart data');
                return;
            }
        }

        if (this.cartCountElement) {
            this.cartCountElement.textContent = count;
            this.cartCountElement.style.display = count > 0 ? 'inline' : 'none';
        }

        if (this.cartTotalElement) {
            this.cartTotalElement.textContent = `$${total}`;
        }

        // Update cart badge
        const cartBadges = document.querySelectorAll('.cart-badge');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        });
    }

    removeCartItemFromDOM(cartItemId) {
        const cartRow = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('tr, .cart-item');
        if (cartRow) {
            cartRow.style.transition = 'opacity 0.3s ease';
            cartRow.style.opacity = '0';
            setTimeout(() => cartRow.remove(), 300);
        }
    }

    clearCartDOM() {
        const cartItems = document.querySelectorAll('.cart-item, .cart-row');
        cartItems.forEach(item => item.remove());
        
        const emptyMessage = document.querySelector('.cart-empty-message');
        if (emptyMessage) {
            emptyMessage.style.display = 'block';
        }
    }

    updateItemTotal(cartItemId, quantity) {
        const priceElement = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('tr, .cart-item').querySelector('.item-price');
        const totalElement = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('tr, .cart-item').querySelector('.item-total');
        
        if (priceElement && totalElement) {
            const price = parseFloat(priceElement.dataset.price);
            const total = (price * quantity).toFixed(2);
            totalElement.textContent = `$${total}`;
        }
    }

    animateCartIcon() {
        const cartIcon = document.querySelector('.cart-icon');
        if (cartIcon) {
            cartIcon.classList.add('cart-bounce');
            setTimeout(() => cartIcon.classList.remove('cart-bounce'), 600);
        }
    }

    async makeRequest(url, method, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (data && (method === 'POST' || method === 'PATCH' || method === 'PUT')) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        return await response.json();
    }

    showNotification(type, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: type === 'success' ? 'Success' : 'Error',
                message: message,
                position: 'topRight',
                timeout: 3000
            });
        } else {
            // Fallback to alert if iziToast is not available
            alert(message);
        }
    }
}

// Initialize cart manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
});
```

## 7. CSS for Cart Animations

```css
/* public/css/cart-animations.css */
.cart-bounce {
    animation: cartBounce 0.6s ease-in-out;
}

@keyframes cartBounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.cart-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.cart-item.removing {
    opacity: 0;
    transform: translateX(-100%);
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.quantity-btn:hover {
    background-color: #f5f5f5;
}

.quantity-input {
    width: 60px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 4px;
}

.cart-badge {
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    position: absolute;
    top: -8px;
    right: -8px;
}
```

## 8. Service Provider Registration

Register the CartService in your AppServiceProvider:

```php
// app/Providers/AppServiceProvider.php
use App\Services\CartService;

public function register()
{
    $this->app->singleton(CartService::class);
}
```

## 9. Middleware for Guest Cart Merging

```bash
php artisan make:middleware MergeGuestCart
```

```php
// app/Http/Middleware/MergeGuestCart.php
<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MergeGuestCart
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->session()->has('guest_session_id')) {
            $this->cartService->mergeGuestCart($request->session()->get('guest_session_id'));
            $request->session()->forget('guest_session_id');
        }

        if (!Auth::check() && !$request->session()->has('guest_session_id')) {
            $request->session()->put('guest_session_id', $request->session()->getId());
        }

        return $next($request);
    }
}
```

Register in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\MergeGuestCart::class,
    ],
];
```

## 10. Usage in Blade Templates

### Product Listing/Details Page

```html
<!-- Add this to your product cards or product detail pages -->
<form class="add-to-cart-form">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    
    <!-- Product options (if any) -->
    <select name="size">
        <option value="S">Small</option>
        <option value="M">Medium</option>
        <option value="L">Large</option>
    </select>
    
    <div class="quantity-controls">
        <button type="button" class="quantity-btn quantity-decrease">-</button>
        <input type="number" name="quantity" value="1" min="1" class="quantity-input">
        <button type="button" class="quantity-btn quantity-increase">+</button>
    </div>
    
    <button type="button" class="add-to-cart-btn" data-product-id="{{ $product->id }}">
        Add to Cart
    </button>
</form>
```

### Cart Page

```html
<!-- Cart items table -->
<div class="cart-items">
    @forelse($items as $item)
        <div class="cart-item" data-cart-item-id="{{ $item->id }}">
            <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}">
            <h4>{{ $item->product_name }}</h4>
            
            <div class="quantity-controls">
                <button class="quantity-btn quantity-decrease" data-cart-item-id="{{ $item->id }}">-</button>
                <input type="number" class="quantity-input" value="{{ $item->quantity }}" data-cart-item-id="{{ $item->id }}">
                <button class="quantity-btn quantity-increase" data-cart-item-id="{{ $item->id }}">+</button>
            </div>
            
            <span class="item-price" data-price="{{ $item->product_price }}">${{ $item->product_price }}</span>
            <span class="item-total">${{ number_format($item->total, 2) }}</span>
            
            <button class="remove-item-btn" data-cart-item-id="{{ $item->id }}">Remove</button>
        </div>
    @empty
        <div class="cart-empty-message">Your cart is empty</div>
    @endforelse
</div>

<div class="cart-summary">
    <div class="cart-total">Total: ${{ number_format($total, 2) }}</div>
    <button class="clear-cart-btn">Clear Cart</button>
</div>
```

### Layout Header (Cart Icon)

```html
<div class="cart-icon-container">
    <a href="{{ route('cart.index') }}" class="cart-icon">
        🛒
        <span class="cart-badge cart-count">0</span>
    </a>
</div>
```

## 11. Include Required Assets

In your layout file:

```html
<head>
    <!-- iziToast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <!-- Your custom cart CSS -->
    <link rel="stylesheet" href="{{ asset('css/cart-animations.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <!-- Your content -->
    
    <!-- iziToast JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <!-- Cart Manager JS -->
    <script src="{{ asset('js/cart-manager.js') }}"></script>
</body>
```

## 12. Running the System

1. Run the migration:
```bash
php artisan migrate
```

2. Make sure your routes are cached:
```bash
php artisan route:cache
```

3. Test the cart functionality by:
   - Adding products to cart
   - Updating quantities
   - Removing items
   - Clearing the cart
   - Testing with both guest and authenticated users

## Features Included

- ✅ Guest cart support with session-based storage
- ✅ User cart with database persistence
- ✅ Automatic guest cart merging on login
- ✅ Real-time cart updates without page refresh
- ✅ Product options/variants support
- ✅ Quantity management with validation
- ✅ Cart item removal and cart clearing
- ✅ iziToast notifications for user feedback
- ✅ Smooth animations and transitions
- ✅ Mobile-friendly responsive design
- ✅ CSRF protection
- ✅ Error handling and validation

This system provides a solid foundation for a modern e-commerce cart that you can extend with additional features like discount codes, shipping calculations, or inventory checking.