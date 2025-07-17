# Modern Laravel 12 Cart System Guide

## Overview
This guide covers creating a modern, component-based cart system for your urbanist ecommerce website using Laravel 12, vanilla JavaScript, and iziToast notifications.

## Architecture
- **CartService**: Handles all cart logic and database operations
- **CartComposer**: Shares cart data across views
- **Components**: Modular Blade components for cart functionality
- **JavaScript**: Vanilla JS for dynamic interactions
- **Storage**: Session-based cart with optional database persistence

## 1. Database Migration

```php
<?php
// database/migrations/create_cart_items_table.php

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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->json('product_options')->nullable(); // size, color, etc.
            $table->timestamps();
            
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

```php
<?php
// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'product_options'
    ];

    protected $casts = [
        'product_options' => 'array',
        'price' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
```

## 3. Cart Service

```php
<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }
        
        return ['session_id' => Session::getId()];
    }

    public function getCartItems()
    {
        return CartItem::where($this->getCartIdentifier())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addToCart(int $productId, int $quantity = 1, array $options = []): bool
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return false;
        }

        $identifier = $this->getCartIdentifier();
        $cartItem = CartItem::where($identifier)
            ->where('product_id', $productId)
            ->where('product_options', json_encode($options))
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create(array_merge($identifier, [
                'product_id' => $productId,
                'product_name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'product_options' => $options
            ]));
        }

        return true;
    }

    public function updateQuantity(int $cartItemId, int $quantity): bool
    {
        $cartItem = CartItem::where($this->getCartIdentifier())
            ->where('id', $cartItemId)
            ->first();

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($cartItemId);
        }

        $cartItem->update(['quantity' => $quantity]);
        return true;
    }

    public function removeFromCart(int $cartItemId): bool
    {
        return CartItem::where($this->getCartIdentifier())
            ->where('id', $cartItemId)
            ->delete() > 0;
    }

    public function clearCart(): bool
    {
        return CartItem::where($this->getCartIdentifier())->delete() > 0;
    }

    public function getCartCount(): int
    {
        return CartItem::where($this->getCartIdentifier())->sum('quantity');
    }

    public function getCartTotal(): float
    {
        return CartItem::where($this->getCartIdentifier())
            ->get()
            ->sum('subtotal');
    }

    public function getCartSummary(): array
    {
        $items = $this->getCartItems();
        
        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'total' => $items->sum('subtotal'),
            'isEmpty' => $items->isEmpty()
        ];
    }

    public function mergeGuestCart(string $sessionId): void
    {
        if (!Auth::check()) {
            return;
        }

        $guestItems = CartItem::where('session_id', $sessionId)->get();
        
        foreach ($guestItems as $guestItem) {
            $existingItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $guestItem->product_id)
                ->where('product_options', $guestItem->product_options)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $guestItem->quantity);
            } else {
                $guestItem->update([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
            }
        }

        CartItem::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();
    }
}
```

## 4. Cart Controller

```php
<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'integer|min:1|max:99',
            'options' => 'array'
        ]);

        $success = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity ?? 1,
            $request->options ?? []
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart' => $this->cartService->getCartSummary()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to add product to cart'
        ], 400);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:99'
        ]);

        $success = $this->cartService->updateQuantity(
            $request->cart_item_id,
            $request->quantity
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart' => $this->cartService->getCartSummary()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update cart'
        ], 400);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer'
        ]);

        $success = $this->cartService->removeFromCart($request->cart_item_id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart' => $this->cartService->getCartSummary()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove item'
        ], 400);
    }

    public function clear(): JsonResponse
    {
        $success = $this->cartService->clearCart();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Cart cleared' : 'Failed to clear cart',
            'cart' => $this->cartService->getCartSummary()
        ]);
    }

    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'cart' => $this->cartService->getCartSummary()
        ]);
    }
}
```

## 5. Cart Composer

```php
<?php
// app/View/Composers/CartComposer.php

namespace App\View\Composers;

use App\Services\CartService;
use Illuminate\View\View;

class CartComposer
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function compose(View $view): void
    {
        $view->with('cartData', $this->cartService->getCartSummary());
    }
}
```

## 6. Service Provider Registration

```php
<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\CartService;
use App\View\Composers\CartComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartService::class);
    }

    public function boot(): void
    {
        // Share cart data with specific components
        View::composer([
            'components.cart.cart-button',
            'components.cart.cart-menu',
            'layouts.app'
        ], CartComposer::class);
    }
}
```

## 7. Routes

```php
<?php
// routes/web.php

use App\Http\Controllers\CartController;

Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
});
```

## 8. Blade Components

### Add to Cart Button Component

```blade
{{-- resources/views/components/cart/add-to-cart-button.blade.php --}}

@props([
    'product',
    'quantity' => 1,
    'options' => [],
    'class' => '',
    'text' => 'Add to Cart'
])

<button 
    type="button"
    class="add-to-cart-btn {{ $class }}"
    data-product-id="{{ $product->id }}"
    data-quantity="{{ $quantity }}"
    data-options="{{ json_encode($options) }}"
    {{ $attributes }}
>
    <span class="btn-text">{{ $text }}</span>
    <span class="btn-loading" style="display: none;">
        <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 2a10 10 0 0 1 10 10"></path>
        </svg>
    </span>
</button>
```

### Cart Button Component

```blade
{{-- resources/views/components/cart/cart-button.blade.php --}}

<button 
    type="button" 
    class="cart-toggle-btn"
    data-cart-count="{{ $cartData['count'] }}"
    onclick="toggleCartMenu()"
>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="m1 1 4 4 8 12 5.5 0 3.5-7H6"></path>
    </svg>
    
    @if($cartData['count'] > 0)
        <span class="cart-count" id="cart-count">{{ $cartData['count'] }}</span>
    @endif
</button>
```

### Cart Menu Component

```blade
{{-- resources/views/components/cart/cart-menu.blade.php --}}
<div id="cart-menu" class="cart-menu" style="display: none;">
    <div class="cart-header">
        <h3>Shopping Cart</h3>
        <button type="button" class="cart-close" onclick="toggleCartMenu()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <div id="cart-items">
            @if($cartData['isEmpty'])
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                </div>
            @else
                @foreach($cartData['items'] as $item)
                    <div class="cart-item" data-item-id="{{ $item->id }}">
                        <div class="item-image">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}">
                        </div>
                        
                        <div class="item-details">
                            <h4>{{ $item->product_name }}</h4>
                            
                            @if($item->product_options)
                                <div class="item-options">
                                    @foreach($item->product_options as $key => $value)
                                        <span>{{ ucfirst($key) }}: {{ $value }}</span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="item-price">${{ number_format($item->price, 2) }}</div>
                        </div>
                        
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button type="button" class="qty-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">-</button>
                                <span class="quantity">{{ $item->quantity }}</span>
                                <button type="button" class="qty-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                            </div>
                            
                            <button type="button" class="remove-btn" onclick="removeFromCart({{ $item->id }})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="3,6 5,6 21,6"></polyline>
                                    <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        @if(!$cartData['isEmpty'])
            <div class="cart-footer">
                <div class="cart-total">
                    <strong>Total: $<span id="cart-total">{{ number_format($cartData['total'], 2) }}</span></strong>
                </div>
                
                <div class="cart-actions">
                    <button type="button" class="btn-secondary" onclick="clearCart()">Clear Cart</button>
                    <a href="{{ route('checkout') }}" class="btn-primary">Checkout</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div id="cart-overlay" class="cart-overlay" style="display: none;" onclick="toggleCartMenu()"></div>
```

## 9. JavaScript Cart Manager

```javascript
// public/js/cart-manager.js

class CartManager {
    constructor() {
        this.isLoading = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCartData();
    }

    bindEvents() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart-btn')) {
                this.handleAddToCart(e.target.closest('.add-to-cart-btn'));
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeCartMenu();
            }
        });
    }

    async handleAddToCart(button) {
        if (this.isLoading) return;

        const productId = button.dataset.productId;
        const quantity = parseInt(button.dataset.quantity) || 1;
        const options = JSON.parse(button.dataset.options || '{}');

        this.setButtonLoading(button, true);

        try {
            const response = await this.apiCall('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    options: options
                })
            });

            if (response.success) {
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to add item to cart', 'error');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async updateQuantity(itemId, quantity) {
        if (this.isLoading) return;

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/update', {
                method: 'PATCH',
                body: JSON.stringify({
                    cart_item_id: itemId,
                    quantity: quantity
                })
            });

            if (response.success) {
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to update cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async removeFromCart(itemId) {
        if (this.isLoading) return;

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/remove', {
                method: 'DELETE',
                body: JSON.stringify({
                    cart_item_id: itemId
                })
            });

            if (response.success) {
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to remove item', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async clearCart() {
        if (this.isLoading) return;

        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/clear', {
                method: 'DELETE'
            });

            if (response.success) {
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to clear cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async loadCartData() {
        try {
            const response = await this.apiCall('/cart/summary');
            if (response.success) {
                this.updateCartUI(response.cart);
            }
        } catch (error) {
            console.error('Failed to load cart data:', error);
        }
    }

    updateCartUI(cartData) {
        // Update cart count
        const cartCountElement = document.getElementById('cart-count');
        const cartButton = document.querySelector('.cart-toggle-btn');
        
        if (cartData.count > 0) {
            if (cartCountElement) {
                cartCountElement.textContent = cartData.count;
                cartCountElement.style.display = 'block';
            } else {
                // Create count element if it doesn't exist
                const countSpan = document.createElement('span');
                countSpan.className = 'cart-count';
                countSpan.id = 'cart-count';
                countSpan.textContent = cartData.count;
                cartButton.appendChild(countSpan);
            }
        } else {
            if (cartCountElement) {
                cartCountElement.style.display = 'none';
            }
        }

        // Update cart total
        const cartTotalElement = document.getElementById('cart-total');
        if (cartTotalElement) {
            cartTotalElement.textContent = this.formatPrice(cartData.total);
        }

        // Update cart button data
        if (cartButton) {
            cartButton.dataset.cartCount = cartData.count;
        }

        // Refresh cart menu if open
        if (this.isCartMenuOpen()) {
            this.refreshCartMenu();
        }
    }

    toggleCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartMenu.style.display === 'none' || !cartMenu.style.display) {
            this.openCartMenu();
        } else {
            this.closeCartMenu();
        }
    }

    openCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        cartMenu.style.display = 'block';
        cartOverlay.style.display = 'block';
        
        // Add animation class
        setTimeout(() => {
            cartMenu.classList.add('cart-menu-open');
        }, 10);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    closeCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        cartMenu.classList.remove('cart-menu-open');
        
        setTimeout(() => {
            cartMenu.style.display = 'none';
            cartOverlay.style.display = 'none';
        }, 300);
        
        // Restore body scroll
        document.body.style.overflow = '';
    }

    isCartMenuOpen() {
        const cartMenu = document.getElementById('cart-menu');
        return cartMenu.style.display === 'block';
    }

    async refreshCartMenu() {
        try {
            const response = await fetch('/cart/summary');
            const data = await response.json();
            
            if (data.success) {
                // Reload the cart menu component
                location.reload(); // Simple approach, or implement dynamic HTML update
            }
        } catch (error) {
            console.error('Failed to refresh cart menu:', error);
        }
    }

    setButtonLoading(button, loading) {
        const text = button.querySelector('.btn-text');
        const loader = button.querySelector('.btn-loading');
        
        if (loading) {
            text.style.display = 'none';
            loader.style.display = 'inline-block';
            button.disabled = true;
        } else {
            text.style.display = 'inline-block';
            loader.style.display = 'none';
            button.disabled = false;
        }
    }

    async apiCall(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        const response = await fetch(url, { ...defaultOptions, ...options });
        return await response.json();
    }

    showNotification(message, type = 'info') {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: type === 'success' ? 'Success' : 'Error',
                message: message,
                position: 'topRight',
                timeout: 3000
            });
        } else {
            alert(message); // Fallback
        }
    }

    formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
    }
}

// Global functions for inline event handlers
window.updateQuantity = (itemId, quantity) => cartManager.updateQuantity(itemId, quantity);
window.removeFromCart = (itemId) => cartManager.removeFromCart(itemId);
window.clearCart = () => cartManager.clearCart();
window.toggleCartMenu = () => cartManager.toggleCartMenu();

// Initialize cart manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.cartManager = new CartManager();
});
```

## 10. Usage Examples

### In your product listing/detail blade:

```blade
<x-cart.add-to-cart-button 
    :product="$product" 
    :quantity="1"
    :options="['size' => 'M', 'color' => 'red']"
    class="btn btn-primary"
    text="Add to Cart"
/>
```

### In your layout header:

```blade
<x-cart.cart-button />
<x-cart.cart-menu />
```

### Include JavaScript and CSS in your layout:

```blade
{{-- In your layout head --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Before closing body tag --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script src="{{ asset('js/cart-manager.js') }}"></script>
```

## 11. Authentication Integration

Add this to your login event listener to merge guest cart:

```php
// In your LoginController or wherever you handle successful login
use App\Services\CartService;

public function authenticated(Request $request, $user)
{
    $cartService = app(CartService::class);
    $cartService->mergeGuestCart(Session::getId());
}
```

## Key Features

✅ **Session-based cart** with optional user persistence  
✅ **Component-based architecture** for reusability  
✅ **Real-time updates** with vanilla JavaScript  
✅ **Guest cart merging** on login  
✅ **Quantity controls** with validation  
✅ **Product options** support (size, color, etc.)  
✅ **Toast notifications** with iziToast  
✅ **Modern ES6+ JavaScript** with async/await  
✅ **CSRF protection** for all requests  
✅ **Loading states** and error handling  
✅ **Keyboard shortcuts** (ESC to close menu)  
✅ **Mobile-friendly** design considerations

This system provides a robust, scalable foundation for your ecommerce cart functionality while maintaining clean, modern code practices.