# Modern Cart System for Laravel 12 Urbanist Ecommerce

A complete guide to implementing a modern cart system using Laravel 12, Vanilla JavaScript, IziToast notifications, and component-based architecture.

## Table of Contents
1. [Backend Setup](#backend-setup)
2. [Cart Model & Migration](#cart-model--migration)
3. [Cart Controller](#cart-controller)
4. [Routes](#routes)
5. [Frontend Components](#frontend-components)
6. [JavaScript Cart Logic](#javascript-cart-logic)
7. [Integration Guide](#integration-guide)

## Backend Setup

### 1. Cart Model & Migration

First, create the cart model and migration:

```bash
php artisan make:model Cart -m
```

**Migration: `database/migrations/xxxx_create_carts_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
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
        Schema::dropIfExists('carts');
    }
};
```

**Cart Model: `app/Models/Cart.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cart extends Model
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

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForCurrentUser($query)
    {
        return $query->where(function ($q) {
            if (Auth::check()) {
                $q->where('user_id', Auth::id());
            } else {
                $q->where('session_id', session()->getId());
            }
        });
    }

    // Helper methods
    public static function getCartItems()
    {
        return self::forCurrentUser()->with('product')->get();
    }

    public static function getCartCount()
    {
        return self::forCurrentUser()->sum('quantity');
    }

    public static function getCartTotal()
    {
        return self::forCurrentUser()->get()->sum(function ($item) {
            return $item->product_price * $item->quantity;
        });
    }

    public function getSubtotalAttribute()
    {
        return $this->product_price * $this->quantity;
    }
}
```

### 2. Cart Controller

**Controller: `app/Http/Controllers/CartController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::getCartItems();
        $cartCount = Cart::getCartCount();
        $cartTotal = Cart::getCartTotal();

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2),
            'cart_total_raw' => $cartTotal
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'product_options' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);
        
        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        // Check if item already exists in cart
        $existingCartItem = Cart::forCurrentUser()
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Stock limit exceeded.'
                ], 400);
            }

            $existingCartItem->update(['quantity' => $newQuantity]);
            $cartItem = $existingCartItem;
        } else {
            $cartItem = Cart::create([
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image,
                'quantity' => $request->quantity,
                'product_options' => $request->product_options
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_item' => $cartItem,
            'cart_count' => Cart::getCartCount(),
            'cart_total' => number_format(Cart::getCartTotal(), 2)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid quantity provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::forCurrentUser()->findOrFail($id);
        $product = $cartItem->product;

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_item' => $cartItem,
            'cart_count' => Cart::getCartCount(),
            'cart_total' => number_format(Cart::getCartTotal(), 2)
        ]);
    }

    public function destroy($id)
    {
        $cartItem = Cart::forCurrentUser()->findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => Cart::getCartCount(),
            'cart_total' => number_format(Cart::getCartTotal(), 2)
        ]);
    }

    public function clear()
    {
        Cart::forCurrentUser()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => '0.00'
        ]);
    }

    public function count()
    {
        return response()->json([
            'success' => true,
            'cart_count' => Cart::getCartCount()
        ]);
    }
}
```

### 3. Routes

**Routes: `routes/web.php`**

```php
<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'store'])->name('store');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});
```

## Frontend Components

### 4. Cart Button Component

**Blade Component: `resources/views/components/cart-button.blade.php`**

```blade
<div class="cart-button-wrapper">
    <button 
        type="button" 
        class="cart-button" 
        onclick="CartUI.toggleCart()"
        aria-label="Open cart"
    >
        <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L6 5H2m5 8a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        <span class="cart-count" id="cart-count">0</span>
    </button>
</div>
```

### 5. Add to Cart Button Component

**Blade Component: `resources/views/components/add-to-cart-button.blade.php`**

```blade
@props(['product'])

<button 
    type="button" 
    class="add-to-cart-btn" 
    data-product-id="{{ $product->id }}"
    data-product-name="{{ $product->name }}"
    data-product-price="{{ $product->price }}"
    data-product-stock="{{ $product->stock }}"
    onclick="Cart.addToCart(this)"
    {{ $product->stock <= 0 ? 'disabled' : '' }}
>
    <span class="btn-text">
        {{ $product->stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
    </span>
    <span class="btn-loading" style="display: none;">
        <svg class="loading-spinner" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
            </circle>
        </svg>
    </span>
</button>
```

### 6. Cart Menu Component

**Blade Component: `resources/views/components/cart-menu.blade.php`**

```blade
<div class="cart-overlay" id="cart-overlay" onclick="CartUI.closeCart()"></div>

<div class="cart-menu" id="cart-menu">
    <div class="cart-header">
        <h3>Shopping Cart</h3>
        <button type="button" class="cart-close" onclick="CartUI.closeCart()" aria-label="Close cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="cart-content">
        <div class="cart-empty" id="cart-empty" style="display: none;">
            <svg class="empty-cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <p>Your cart is empty</p>
            <button type="button" class="continue-shopping-btn" onclick="CartUI.closeCart()">
                Continue Shopping
            </button>
        </div>

        <div class="cart-items" id="cart-items">
            <!-- Cart items will be populated here -->
        </div>
    </div>

    <div class="cart-footer" id="cart-footer">
        <div class="cart-total">
            <div class="total-row">
                <span>Subtotal:</span>
                <span class="total-amount" id="cart-total">$0.00</span>
            </div>
        </div>
        
        <div class="cart-actions">
            <button type="button" class="clear-cart-btn" onclick="Cart.clearCart()">
                Clear Cart
            </button>
            <button type="button" class="checkout-btn" onclick="Cart.proceedToCheckout()">
                Proceed to Checkout
            </button>
        </div>
    </div>
</div>
```

## JavaScript Cart Logic

### 7. Main Cart JavaScript

**JavaScript: `public/js/cart.js`**

```javascript
// Main Cart Class
class CartSystem {
    constructor() {
        this.isLoading = false;
        this.cartData = {
            items: [],
            count: 0,
            total: '0.00'
        };
        this.init();
    }

    init() {
        this.loadCartData();
        this.bindEvents();
        
        // Initialize IziToast if not already done
        if (typeof iziToast !== 'undefined') {
            iziToast.settings({
                timeout: 3000,
                resetOnHover: true,
                transitionIn: 'fadeIn',
                transitionOut: 'fadeOut',
                position: 'topRight'
            });
        }
    }

    bindEvents() {
        // Listen for page load
        document.addEventListener('DOMContentLoaded', () => {
            this.loadCartData();
        });

        // Listen for auth state changes
        document.addEventListener('authStateChanged', () => {
            this.loadCartData();
        });
    }

    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };

        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error('Cart request failed:', error);
            this.showNotification('error', error.message || 'Something went wrong');
            throw error;
        }
    }

    async loadCartData() {
        try {
            const data = await this.makeRequest('/cart');
            this.cartData = data;
            this.updateCartUI();
        } catch (error) {
            console.error('Failed to load cart data:', error);
        }
    }

    async addToCart(button) {
        if (this.isLoading) return;

        const productId = button.dataset.productId;
        const quantity = 1; // Default quantity, can be modified
        
        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const data = await this.makeRequest('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            this.updateCartCount();
            this.showNotification('success', data.message);
            
            // Reload full cart data
            await this.loadCartData();
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async updateCartItem(itemId, quantity) {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const data = await this.makeRequest(`/cart/${itemId}`, {
                method: 'PUT',
                body: JSON.stringify({ quantity })
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            this.updateCartUI();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Reload cart data to reset UI
            this.loadCartData();
        } finally {
            this.isLoading = false;
        }
    }

    async removeFromCart(itemId) {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const data = await this.makeRequest(`/cart/${itemId}`, {
                method: 'DELETE'
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            await this.loadCartData();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.isLoading = false;
        }
    }

    async clearCart() {
        if (this.isLoading) return;
        
        // Confirm before clearing
        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }

        this.isLoading = true;

        try {
            const data = await this.makeRequest('/cart', {
                method: 'DELETE'
            });

            this.cartData = {
                items: [],
                count: 0,
                total: '0.00'
            };
            
            this.updateCartUI();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.isLoading = false;
        }
    }

    updateCartUI() {
        this.updateCartCount();
        this.updateCartItems();
        this.updateCartTotal();
        this.toggleEmptyState();
    }

    updateCartCount() {
        const countElements = document.querySelectorAll('#cart-count, .cart-count');
        countElements.forEach(element => {
            element.textContent = this.cartData.cart_count || 0;
        });
    }

    updateCartItems() {
        const cartItemsContainer = document.getElementById('cart-items');
        if (!cartItemsContainer) return;

        if (!this.cartData.cart_items || this.cartData.cart_items.length === 0) {
            cartItemsContainer.innerHTML = '';
            return;
        }

        cartItemsContainer.innerHTML = this.cartData.cart_items.map(item => this.renderCartItem(item)).join('');
    }

    renderCartItem(item) {
        return `
            <div class="cart-item" data-item-id="${item.id}">
                <div class="item-image">
                    <img src="${item.product_image || '/images/placeholder.jpg'}" alt="${item.product_name}" loading="lazy">
                </div>
                <div class="item-details">
                    <h4 class="item-name">${item.product_name}</h4>
                    <div class="item-price">$${parseFloat(item.product_price).toFixed(2)}</div>
                    ${item.product_options ? this.renderProductOptions(item.product_options) : ''}
                </div>
                <div class="item-quantity">
                    <button type="button" class="qty-btn qty-minus" onclick="Cart.changeQuantity(${item.id}, ${item.quantity - 1})">-</button>
                    <input type="number" class="qty-input" value="${item.quantity}" min="1" max="100" 
                           onchange="Cart.changeQuantity(${item.id}, this.value)" readonly>
                    <button type="button" class="qty-btn qty-plus" onclick="Cart.changeQuantity(${item.id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-total">$${(parseFloat(item.product_price) * item.quantity).toFixed(2)}</div>
                <button type="button" class="remove-item" onclick="Cart.removeFromCart(${item.id})" aria-label="Remove item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="3,6 5,6 21,6"></polyline>
                        <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"></path>
                    </svg>
                </button>
            </div>
        `;
    }

    renderProductOptions(options) {
        if (!options || typeof options !== 'object') return '';
        
        return `
            <div class="item-options">
                ${Object.entries(options).map(([key, value]) => 
                    `<span class="option">${key}: ${value}</span>`
                ).join(', ')}
            </div>
        `;
    }

    updateCartTotal() {
        const totalElements = document.querySelectorAll('#cart-total, .cart-total');
        totalElements.forEach(element => {
            element.textContent = `$${this.cartData.cart_total || '0.00'}`;
        });
    }

    toggleEmptyState() {
        const emptyElement = document.getElementById('cart-empty');
        const itemsElement = document.getElementById('cart-items');
        const footerElement = document.getElementById('cart-footer');
        
        if (!emptyElement || !itemsElement || !footerElement) return;

        const isEmpty = !this.cartData.cart_items || this.cartData.cart_items.length === 0;
        
        emptyElement.style.display = isEmpty ? 'block' : 'none';
        itemsElement.style.display = isEmpty ? 'none' : 'block';
        footerElement.style.display = isEmpty ? 'none' : 'block';
    }

    changeQuantity(itemId, newQuantity) {
        const quantity = parseInt(newQuantity);
        
        if (quantity < 1) {
            this.removeFromCart(itemId);
            return;
        }
        
        if (quantity > 100) {
            this.showNotification('warning', 'Maximum quantity is 100');
            return;
        }
        
        this.updateCartItem(itemId, quantity);
    }

    setButtonLoading(button, isLoading) {
        const textSpan = button.querySelector('.btn-text');
        const loadingSpan = button.querySelector('.btn-loading');
        
        if (textSpan && loadingSpan) {
            textSpan.style.display = isLoading ? 'none' : 'inline';
            loadingSpan.style.display = isLoading ? 'inline' : 'none';
        }
        
        button.disabled = isLoading;
    }

    showNotification(type, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: type.charAt(0).toUpperCase() + type.slice(1),
                message: message
            });
        } else {
            // Fallback to alert if iziToast is not available
            alert(message);
        }
    }

    proceedToCheckout() {
        if (!this.cartData.cart_items || this.cartData.cart_items.length === 0) {
            this.showNotification('warning', 'Your cart is empty');
            return;
        }
        
        // Redirect to checkout page
        window.location.href = '/checkout';
    }
}

// Cart UI Class for handling cart menu
class CartUISystem {
    constructor() {
        this.isOpen = false;
        this.init();
    }

    init() {
        // Bind escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeCart();
            }
        });
    }

    toggleCart() {
        if (this.isOpen) {
            this.closeCart();
        } else {
            this.openCart();
        }
    }

    openCart() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartMenu && cartOverlay) {
            cartMenu.classList.add('cart-open');
            cartOverlay.classList.add('overlay-active');
            document.body.classList.add('cart-menu-open');
            this.isOpen = true;
            
            // Load fresh cart data when opening
            Cart.loadCartData();
        }
    }

    closeCart() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartMenu && cartOverlay) {
            cartMenu.classList.remove('cart-open');
            cartOverlay.classList.remove('overlay-active');
            document.body.classList.remove('cart-menu-open');
            this.isOpen = false;
        }
    }
}

// Initialize cart systems
const Cart = new CartSystem();
const CartUI = new CartUISystem();

// Make cart available globally
window.Cart = Cart;
window.CartUI = CartUI;
```

## Integration Guide

### 8. Include Scripts in Your Layout

**In your main layout file (e.g., `resources/views/layouts/app.blade.php`):**

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    
    <!-- IziToast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    
    <!-- Your existing CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <!-- Your existing header with cart button -->
    <header>
        <nav>
            <!-- Your navigation -->
            <x-cart-button />
        </nav>
    </header>

    <!-- Main content -->
    <main>
        @yield('content')
    </main>

    <!-- Cart menu (always present) -->
    <x-cart-menu />

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    
    <!-- Your existing scripts -->
    @stack('scripts')
</body>
</html>
```

### 9. Using Components in Your Views

**In your product listing/detail views:**

```blade
@extends('layouts.app')

@section('content')
<div class="products-grid">
    @foreach($products as $product)
        <div class="product-card">
            <img src="{{ $product->image }}" alt="{{ $product->name }}">
            <h3>{{ $product->name }}</h3>
            <p class="price">${{ number_format($product->price, 2) }}</p>
            
            <!-- Add to cart component -->
            <x-add-to-cart-button :product="$product" />
        </div>
    @endforeach
</div>
@endsection
```

### 10. Essential CSS Classes (Reference)

Here are the key CSS classes your existing styles should target:

```css
/* Cart Button */
.cart-button-wrapper { }
.cart-button { }
.cart-icon { }
.cart-count { }

/* Add to Cart Button */
.add-to-cart-btn { }
.btn-text { }
.btn-loading { }
.loading-spinner { }

/* Cart Menu */
.cart-overlay { }
.cart-menu { }
.cart-open { }
.overlay-active { }
.cart-menu-open { } /* Applied to body */

/* Cart Content */
.cart-header { }
.cart-close { }
.cart-content { }
.cart-empty { }
.cart-items { }
.cart-footer { }

/* Cart Items */
.cart-item { }
.item-image { }
.item-details { }
.item-name { }
.item-price { }
.item-options { }
.item-quantity { }
.qty-btn { }
.qty-input { }
.item-total { }
.remove-item { }

/* Cart Actions */
.cart-total { }
.total-amount { }
.cart-actions { }
.clear-cart-btn { }
.checkout-btn { }
.continue-shopping-btn { }
```

### 11. Advanced Features & Customizations

#### A. Product Variants Support

If you need to support product variants (size, color, etc.), modify your add to cart button:

```blade
<!-- Enhanced Add to Cart Button with Variants -->
@props(['product'])

<div class="product-options" data-product-id="{{ $product->id }}">
    @if($product->variants->count() > 0)
        @foreach($product->variants as $variantType => $options)
            <div class="variant-group">
                <label>{{ ucfirst($variantType) }}:</label>
                <select class="variant-select" data-variant="{{ $variantType }}" required>
                    <option value="">Select {{ $variantType }}</option>
                    @foreach($options as $option)
                        <option value="{{ $option->value }}" data-price="{{ $option->price_modifier }}">
                            {{ $option->label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endforeach
    @endif
    
    <div class="quantity-selector">
        <label>Quantity:</label>
        <input type="number" class="product-quantity" value="1" min="1" max="{{ $product->stock }}">
    </div>
    
    <x-add-to-cart-button :product="$product" />
</div>
```

Enhanced JavaScript for variants:

```javascript
// Add this method to CartSystem class
addToCartWithVariants(button) {
    const productCard = button.closest('[data-product-id]');
    const productId = productCard.dataset.productId;
    const quantityInput = productCard.querySelector('.product-quantity');
    const variantSelects = productCard.querySelectorAll('.variant-select');
    
    // Collect variants
    const variants = {};
    let allVariantsSelected = true;
    
    variantSelects.forEach(select => {
        if (select.value) {
            variants[select.dataset.variant] = {
                value: select.value,
                label: select.selectedOptions[0].text
            };
        } else {
            allVariantsSelected = false;
        }
    });
    
    if (variantSelects.length > 0 && !allVariantsSelected) {
        this.showNotification('warning', 'Please select all product options');
        return;
    }
    
    const quantity = parseInt(quantityInput?.value || 1);
    
    this.addToCartWithOptions(button, productId, quantity, variants);
}

async addToCartWithOptions(button, productId, quantity, options = {}) {
    if (this.isLoading) return;
    
    this.setButtonLoading(button, true);
    this.isLoading = true;

    try {
        const data = await this.makeRequest('/cart/add', {
            method: 'POST',
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity,
                product_options: options
            })
        });

        this.cartData.count = data.cart_count;
        this.cartData.total = data.cart_total;
        
        this.updateCartCount();
        this.showNotification('success', data.message);
        
        await this.loadCartData();
        
    } catch (error) {
        // Error handling is done in makeRequest
    } finally {
        this.setButtonLoading(button, false);
        this.isLoading = false;
    }
}
```

#### B. Cart Persistence for Guest Users

Add session-based cart persistence by modifying your Cart model:

```php
// Add this method to Cart model
public static function mergeCarts($sessionId, $userId)
{
    // Get session cart items
    $sessionItems = self::where('session_id', $sessionId)
                       ->whereNull('user_id')
                       ->get();
    
    foreach ($sessionItems as $sessionItem) {
        // Check if user already has this item
        $existingItem = self::where('user_id', $userId)
                           ->where('product_id', $sessionItem->product_id)
                           ->where('product_options', $sessionItem->product_options)
                           ->first();
        
        if ($existingItem) {
            // Merge quantities
            $existingItem->increment('quantity', $sessionItem->quantity);
            $sessionItem->delete();
        } else {
            // Transfer to user
            $sessionItem->update(['user_id' => $userId]);
        }
    }
}
```

Add this to your login controller:

```php
// After successful login
if (Auth::check()) {
    Cart::mergeCarts(session()->getId(), Auth::id());
}
```

#### C. Real-time Cart Updates

For real-time updates across tabs, add this to your CartSystem:

```javascript
// Add to CartSystem constructor
constructor() {
    // ... existing code ...
    this.setupStorageListener();
}

setupStorageListener() {
    // Listen for storage events (works across tabs)
    window.addEventListener('storage', (e) => {
        if (e.key === 'cart_updated') {
            this.loadCartData();
        }
    });
}

// Add after successful cart operations
triggerCartUpdate() {
    // Trigger event for other tabs
    localStorage.setItem('cart_updated', Date.now().toString());
    localStorage.removeItem('cart_updated');
}
```

#### D. Cart Analytics Integration

Add analytics tracking to your cart operations:

```javascript
// Add to CartSystem class
trackCartEvent(action, productId, quantity = 1, value = 0) {
    // Google Analytics 4
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            currency: 'USD',
            value: value,
            items: [{
                item_id: productId,
                quantity: quantity
            }]
        });
    }
    
    // Facebook Pixel
    if (typeof fbq !== 'undefined') {
        fbq('track', action, {
            content_ids: [productId],
            content_type: 'product',
            value: value,
            currency: 'USD'
        });
    }
}

// Use in cart methods
async addToCart(button) {
    // ... existing code ...
    
    if (data.success) {
        this.trackCartEvent('add_to_cart', productId, quantity, data.cart_item.subtotal);
    }
}
```

#### E. Cart Abandonment Recovery

Add cart abandonment tracking:

```php
// Create migration for abandoned carts
php artisan make:migration create_abandoned_carts_table

// Migration content
Schema::create('abandoned_carts', function (Blueprint $table) {
    $table->id();
    $table->string('session_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('email')->nullable();
    $table->json('cart_data');
    $table->decimal('cart_total', 10, 2);
    $table->timestamp('abandoned_at');
    $table->boolean('recovered')->default(false);
    $table->timestamps();
});
```

Add scheduled task to detect abandoned carts:

```php
// In App\Console\Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $abandonedCarts = Cart::where('updated_at', '<', now()->subHours(2))
                             ->with('user')
                             ->get()
                             ->groupBy(function ($item) {
                                 return $item->user_id ?: $item->session_id;
                             });
        
        foreach ($abandonedCarts as $cartItems) {
            // Create abandoned cart record and send email
            AbandonedCart::createFromCartItems($cartItems);
        }
    })->hourly();
}
```

### 12. Testing Your Cart System

#### A. Feature Tests

Create comprehensive tests for your cart functionality:

```php
<?php
// tests/Feature/CartTest.php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_product_to_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);
        
        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        $response->assertOk()
                ->assertJson([
                    'success' => true,
                    'cart_count' => 2
                ]);
        
        $this->assertDatabaseHas('carts', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }
    
    public function test_authenticated_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        
        $response = $this->actingAs($user)
                        ->postJson('/cart/add', [
                            'product_id' => $product->id,
                            'quantity' => 1
                        ]);
        
        $response->assertOk();
        
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
    }
    
    public function test_cannot_add_out_of_stock_product()
    {
        $product = Product::factory()->create(['stock' => 0]);
        
        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ]);
    }
    
    public function test_can_update_cart_item_quantity()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = Cart::factory()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'session_id' => session()->getId()
        ]);
        
        $response = $this->putJson("/cart/{$cartItem->id}", [
            'quantity' => 3
        ]);
        
        $response->assertOk();
        
        $this->assertDatabaseHas('carts', [
            'id' => $cartItem->id,
            'quantity' => 3
        ]);
    }
    
    public function test_can_remove_cart_item()
    {
        $cartItem = Cart::factory()->create([
            'session_id' => session()->getId()
        ]);
        
        $response = $this->deleteJson("/cart/{$cartItem->id}");
        
        $response->assertOk();
        
        $this->assertDatabaseMissing('carts', [
            'id' => $cartItem->id
        ]);
    }
    
    public function test_can_clear_entire_cart()
    {
        Cart::factory()->count(3)->create([
            'session_id' => session()->getId()
        ]);
        
        $response = $this->deleteJson('/cart');
        
        $response->assertOk();
        
        $this->assertEquals(0, Cart::forCurrentUser()->count());
    }
}
```

#### B. Browser Tests (Laravel Dusk)

```php
<?php
// tests/Browser/CartTest.php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    public function test_user_can_add_product_to_cart_via_ui()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 29.99,
            'stock' => 10
        ]);
        
        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products')
                   ->click("[data-product-id='{$product->id}']")
                   ->waitForText('Item added to cart successfully')
                   ->assertSee('1') // Cart count
                   ->click('.cart-button')
                   ->waitFor('.cart-menu.cart-open')
                   ->assertSee('Test Product')
                   ->assertSee('$29.99');
        });
    }
    
    public function test_user_can_update_quantity_in_cart()
    {
        // Test implementation
    }
    
    public function test_user_can_remove_item_from_cart()
    {
        // Test implementation
    }
}
```

### 13. Performance Optimization

#### A. Database Optimization

Add indexes for better performance:

```php
// Add to your cart migration
$table->index(['session_id', 'user_id', 'product_id']);
$table->index(['updated_at']); // For abandoned cart cleanup
```

#### B. Caching Strategy

Implement Redis caching for cart data:

```php
// Add to Cart model
public static function getCachedCartItems($identifier)
{
    $cacheKey = "cart:items:{$identifier}";
    
    return Cache::tags(['cart'])->remember($cacheKey, 300, function () {
        return self::getCartItems();
    });
}

public static function clearCartCache($identifier)
{
    $cacheKey = "cart:items:{$identifier}";
    Cache::tags(['cart'])->forget($cacheKey);
}

// Use in controller
public function index()
{
    $identifier = Auth::id() ?: session()->getId();
    $cartItems = Cart::getCachedCartItems($identifier);
    // ... rest of the method
}
```

#### C. JavaScript Optimization

Implement debouncing for quantity updates:

```javascript
// Add to CartSystem class
constructor() {
    // ... existing code ...
    this.updateDebounceTimers = new Map();
}

debounceQuantityUpdate(itemId, quantity, delay = 500) {
    // Clear existing timer
    if (this.updateDebounceTimers.has(itemId)) {
        clearTimeout(this.updateDebounceTimers.get(itemId));
    }
    
    // Set new timer
    const timer = setTimeout(() => {
        this.updateCartItem(itemId, quantity);
        this.updateDebounceTimers.delete(itemId);
    }, delay);
    
    this.updateDebounceTimers.set(itemId, timer);
}

// Use in quantity change
changeQuantity(itemId, newQuantity) {
    const quantity = parseInt(newQuantity);
    
    if (quantity < 1) {
        this.removeFromCart(itemId);
        return;
    }
    
    // Use debounced update instead of immediate update
    this.debounceQuantityUpdate(itemId, quantity);
}
```

### 14. Security Considerations

#### A. CSRF Protection

Ensure all cart operations are protected:

```php
// All routes should be within web middleware group
Route::middleware(['web'])->group(function () {
    Route::prefix('cart')->name('cart.')->group(function () {
        // ... your cart routes
    });
});
```

#### B. Input Validation & Sanitization

Add comprehensive validation:

```php
// In CartController
private function validateCartRequest(Request $request)
{
    return Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1|max:100',
        'product_options' => 'nullable|array|max:10',
        'product_options.*' => 'string|max:255'
    ]);
}
```

#### C. Rate Limiting

Add rate limiting to prevent abuse:

```php
// In RouteServiceProvider or routes file
Route::middleware(['web', 'throttle:60,1'])->group(function () {
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add', [CartController::class, 'store'])->name('store');
        // ... other cart routes
    });
});
```

### 15. Deployment Checklist

Before deploying your cart system:

- [ ] Run all migrations: `php artisan migrate`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Optimize autoloader: `composer dump-autoload --optimize`
- [ ] Run tests: `php artisan test`
- [ ] Check CSRF token is properly included in meta tags
- [ ] Verify IziToast CDN is accessible
- [ ] Test cart functionality across different browsers
- [ ] Verify cart persistence works correctly
- [ ] Test with both authenticated and guest users
- [ ] Confirm cart abandonment cleanup task is scheduled
- [ ] Set up monitoring for cart-related errors

## Troubleshooting Common Issues

### Cart Count Not Updating
- Check CSRF token in meta tags
- Verify JavaScript console for errors
- Ensure cart routes are accessible

### Cart Items Not Persisting
- Check session configuration
- Verify database connections
- Confirm migration has run successfully

### IziToast Not Showing
- Verify CDN is loading
- Check for JavaScript conflicts
- Ensure proper initialization

### Performance Issues
- Implement caching strategy
- Add database indexes
- Use pagination for large carts

This comprehensive guide provides everything you need to implement a modern, robust cart system for your Laravel 12 urbanist ecommerce website. The system is designed to be scalable, maintainable, and user-friendly while following modern web development best practices.