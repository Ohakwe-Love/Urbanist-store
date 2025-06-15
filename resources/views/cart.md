# Modern Cart System for Laravel 12 Ecommerce

A comprehensive guide to building a sophisticated, user-friendly cart system for your urbanist ecommerce website using Laravel 12.

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Database Design](#database-design)
3. [Models & Relationships](#models--relationships)
4. [Cart Service Implementation](#cart-service-implementation)
5. [API Controllers](#api-controllers)
6. [Frontend Integration](#frontend-integration)
7. [Advanced Features](#advanced-features)
8. [Testing](#testing)
9. [Performance Optimization](#performance-optimization)

## Architecture Overview

Our modern cart system will feature:
- **Persistent carts** for authenticated users
- **Session-based carts** for guests
- **Real-time updates** using Laravel broadcasting
- **Inventory validation** and stock management
- **Dynamic pricing** with promotions and discounts
- **Mobile-first responsive design**
- **Progressive Web App** capabilities

## Database Design

### Cart Tables Migration

```php
// database/migrations/create_carts_table.php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->string('session_id')->nullable();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->json('metadata')->nullable(); // For storing additional cart data
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
    
    $table->index(['user_id', 'session_id']);
});

// database/migrations/create_cart_items_table.php
Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cart_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
    $table->integer('quantity');
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total_price', 10, 2);
    $table->json('product_snapshot'); // Store product details at time of adding
    $table->timestamps();
    
    $table->unique(['cart_id', 'product_id', 'variant_id']);
});

// database/migrations/create_product_variants_table.php
Schema::create('product_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('name'); // e.g., "Size: Large, Color: Blue"
    $table->json('attributes'); // {"size": "L", "color": "blue"}
    $table->string('sku')->unique();
    $table->decimal('price_adjustment', 8, 2)->default(0);
    $table->integer('stock_quantity')->default(0);
    $table->timestamps();
});
```

## Models & Relationships

### Cart Model

```php
// app/Models/Cart.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'metadata',
        'expires_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum('total_price');
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->items->count() === 0;
    }

    public function clearExpiredItems(): void
    {
        $this->items()->whereHas('product', function ($query) {
            $query->where('is_active', false)
                  ->orWhere('stock_quantity', '<=', 0);
        })->delete();
    }
}
```

### CartItem Model

```php
// app/Models/CartItem.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_snapshot'
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function updateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    public function getCurrentPrice(): float
    {
        $basePrice = $this->product->price;
        $variantAdjustment = $this->variant?->price_adjustment ?? 0;
        
        return $basePrice + $variantAdjustment;
    }

    public function hasStockAvailable(): bool
    {
        if ($this->variant) {
            return $this->variant->stock_quantity >= $this->quantity;
        }
        
        return $this->product->stock_quantity >= $this->quantity;
    }
}
```

## Cart Service Implementation

### CartService Class

```php
// app/Services/CartService.php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Events\CartUpdated;

class CartService
{
    protected Cart $cart;

    public function __construct()
    {
        $this->cart = $this->getOrCreateCart();
    }

    public function getCart(): Cart
    {
        return $this->cart->load(['items.product', 'items.variant']);
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        // Validate stock availability
        $this->validateStock($product, $variant, $quantity);

        // Check if item already exists in cart
        $existingItem = $this->cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            return $this->updateItemQuantity($existingItem->id, $existingItem->quantity + $quantity);
        }

        // Create new cart item
        $unitPrice = $this->calculatePrice($product, $variant);
        $cartItem = $this->cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'product_snapshot' => $this->createProductSnapshot($product, $variant)
        ]);

        $this->broadcastCartUpdate();
        
        return $cartItem;
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->cart->items()->findOrFail($cartItemId);
        
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        // Validate stock
        $this->validateStock($cartItem->product, $cartItem->variant, $quantity);

        $cartItem->update([
            'quantity' => $quantity,
            'total_price' => $cartItem->unit_price * $quantity
        ]);

        $this->broadcastCartUpdate();
        
        return $cartItem;
    }

    public function removeItem(int $cartItemId): bool
    {
        $cartItem = $this->cart->items()->findOrFail($cartItemId);
        $result = $cartItem->delete();
        
        $this->broadcastCartUpdate();
        
        return $result;
    }

    public function clearCart(): bool
    {
        $result = $this->cart->items()->delete();
        $this->broadcastCartUpdate();
        
        return $result;
    }

    public function syncPrices(): void
    {
        $this->cart->items->each(function (CartItem $item) {
            $currentPrice = $this->calculatePrice($item->product, $item->variant);
            
            if ($item->unit_price != $currentPrice) {
                $item->update([
                    'unit_price' => $currentPrice,
                    'total_price' => $currentPrice * $item->quantity
                ]);
            }
        });

        $this->broadcastCartUpdate();
    }

    public function validateCartItems(): array
    {
        $issues = [];

        $this->cart->items->each(function (CartItem $item) use (&$issues) {
            // Check if product is still available
            if (!$item->product->is_active) {
                $issues[] = [
                    'type' => 'unavailable',
                    'item_id' => $item->id,
                    'message' => "Product '{$item->product->name}' is no longer available"
                ];
                return;
            }

            // Check stock availability
            if (!$item->hasStockAvailable()) {
                $availableStock = $item->variant 
                    ? $item->variant->stock_quantity 
                    : $item->product->stock_quantity;
                    
                $issues[] = [
                    'type' => 'insufficient_stock',
                    'item_id' => $item->id,
                    'available_quantity' => $availableStock,
                    'message' => "Only {$availableStock} items available for '{$item->product->name}'"
                ];
            }

            // Check price changes
            $currentPrice = $this->calculatePrice($item->product, $item->variant);
            if ($item->unit_price != $currentPrice) {
                $issues[] = [
                    'type' => 'price_change',
                    'item_id' => $item->id,
                    'old_price' => $item->unit_price,
                    'new_price' => $currentPrice,
                    'message' => "Price changed for '{$item->product->name}'"
                ];
            }
        });

        return $issues;
    }

    public function mergeCarts(Cart $guestCart): void
    {
        if (!Auth::check()) {
            return;
        }

        DB::transaction(function () use ($guestCart) {
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $this->cart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where('variant_id', $guestItem->variant_id)
                    ->first();

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $guestItem->quantity;
                    $this->updateItemQuantity($existingItem->id, $newQuantity);
                } else {
                    $this->addItem(
                        $guestItem->product_id,
                        $guestItem->quantity,
                        $guestItem->variant_id
                    );
                }
            }

            $guestCart->delete();
        });
    }

    protected function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['expires_at' => now()->addDays(30)]
            );
        }

        $sessionId = Session::getId();
        
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['expires_at' => now()->addDays(7)]
        );
    }

    protected function validateStock(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
        
        if ($quantity > $availableStock) {
            throw new \Exception("Insufficient stock. Only {$availableStock} items available.");
        }
    }

    protected function calculatePrice(Product $product, ?ProductVariant $variant): float
    {
        $basePrice = $product->price;
        $variantAdjustment = $variant?->price_adjustment ?? 0;
        
        // Apply any active promotions here
        // $promotionalPrice = $this->applyPromotions($basePrice + $variantAdjustment, $product);
        
        return $basePrice + $variantAdjustment;
    }

    protected function createProductSnapshot(Product $product, ?ProductVariant $variant): array
    {
        return [
            'name' => $product->name,
            'image' => $product->featured_image,
            'variant_name' => $variant?->name,
            'variant_attributes' => $variant?->attributes,
            'captured_at' => now()->toISOString()
        ];
    }

    protected function broadcastCartUpdate(): void
    {
        broadcast(new CartUpdated($this->cart))->toOthers();
    }
}
```

## API Controllers

### CartController

```php
// app/Http/Controllers/Api/CartController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show(): JsonResponse
    {
        $cart = $this->cartService->getCart();
        
        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'image' => $item->product->featured_image,
                        ],
                        'variant' => $item->variant ? [
                            'id' => $item->variant->id,
                            'name' => $item->variant->name,
                            'attributes' => $item->variant->attributes,
                        ] : null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                }),
                'total' => $cart->total,
                'item_count' => $cart->item_count,
            ]
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $cartItem = $this->cartService->addItem(
                $request->product_id,
                $request->quantity,
                $request->variant_id
            );

            return response()->json([
                'message' => 'Item added to cart successfully',
                'cart_item' => $cartItem
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateItem(Request $request, int $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10'
        ]);

        try {
            if ($request->quantity == 0) {
                $this->cartService->removeItem($cartItemId);
                return response()->json(['message' => 'Item removed from cart']);
            }

            $cartItem = $this->cartService->updateItemQuantity($cartItemId, $request->quantity);

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cart_item' => $cartItem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function removeItem(int $cartItemId): JsonResponse
    {
        try {
            $this->cartService->removeItem($cartItemId);

            return response()->json([
                'message' => 'Item removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function clear(): JsonResponse
    {
        $this->cartService->clearCart();

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function validate(): JsonResponse
    {
        $issues = $this->cartService->validateCartItems();

        return response()->json([
            'valid' => empty($issues),
            'issues' => $issues
        ]);
    }

    public function syncPrices(): JsonResponse
    {
        $this->cartService->syncPrices();

        return response()->json([
            'message' => 'Cart prices synchronized successfully'
        ]);
    }
}
```

## Frontend Integration

### Cart State Management (Using Alpine.js)

```javascript
// resources/js/cart.js
document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        items: [],
        total: 0,
        itemCount: 0,
        loading: false,
        
        async init() {
            await this.fetchCart();
            this.setupWebSocketListener();
        },

        async fetchCart() {
            try {
                this.loading = true;
                const response = await fetch('/api/cart');
                const data = await response.json();
                
                this.items = data.cart.items;
                this.total = data.cart.total;
                this.itemCount = data.cart.item_count;
            } catch (error) {
                console.error('Failed to fetch cart:', error);
            } finally {
                this.loading = false;
            }
        },

        async addItem(productId, quantity = 1, variantId = null) {
            try {
                this.loading = true;
                const response = await fetch('/api/cart/items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity,
                        variant_id: variantId
                    })
                });

                const data = await response.json();
                
                if (response.ok) {
                    await this.fetchCart();
                    this.showNotification('Item added to cart!', 'success');
                } else {
                    this.showNotification(data.error || 'Failed to add item', 'error');
                }
            } catch (error) {
                this.showNotification('Network error occurred', 'error');
            } finally {
                this.loading = false;
            }
        },

        async updateQuantity(itemId, quantity) {
            try {
                const response = await fetch(`/api/cart/items/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ quantity })
                });

                const data = await response.json();
                
                if (response.ok) {
                    await this.fetchCart();
                } else {
                    this.showNotification(data.error || 'Failed to update item', 'error');
                }
            } catch (error) {
                this.showNotification('Network error occurred', 'error');
            }
        },

        async removeItem(itemId) {
            try {
                const response = await fetch(`/api/cart/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    await this.fetchCart();
                    this.showNotification('Item removed from cart', 'success');
                }
            } catch (error) {
                this.showNotification('Failed to remove item', 'error');
            }
        },

        setupWebSocketListener() {
            if (window.Echo) {
                window.Echo.private(`cart.${window.cartId}`)
                    .listen('CartUpdated', (e) => {
                        this.fetchCart();
                    });
            }
        },

        showNotification(message, type) {
            // Implement your notification system here
            // Could use toast notifications, modals, etc.
            Alpine.store('notifications').add({ message, type });
        }
    });
});
```

### Modern Cart Component (Blade + Alpine.js)

```html
<!-- resources/views/components/cart-drawer.blade.php -->
<div x-data="cartDrawer" 
     x-show="$store.cart.open" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-hidden"
     style="display: none;">
     
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50" @click="$store.cart.close()"></div>
    
    <!-- Drawer -->
    <div x-show="$store.cart.open"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl">
         
        <div class="flex h-full flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Shopping Cart</h2>
                <button @click="$store.cart.close()" 
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <template x-if="$store.cart.items.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="text-gray-500">Your cart is empty</p>
                        <button @click="$store.cart.close()" 
                                class="mt-4 bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                            Continue Shopping
                        </button>
                    </div>
                </template>
                
                <div class="space-y-4">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                            <img :src="item.product.image" 
                                 :alt="item.product.name"
                                 class="h-16 w-16 object-cover rounded-md">
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900" x-text="item.product.name"></h4>
                                <p x-show="item.variant" 
                                   class="text-sm text-gray-500" 
                                   x-text="item.variant?.name"></p>
                                <p class="text-sm font-medium text-gray-900" 
                                   x-text="'$' + item.unit_price.toFixed(2)"></p>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button @click="$store.cart.updateQuantity(item.id, Math.max(0, item.quantity - 1))"
                                        class="h-8 w-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                    <span class="text-sm">−</span>
                                </button>
                                <span class="text-sm font-medium w-8 text-center" x-text="item.quantity"></span>
                                <button @click="$store.cart.updateQuantity(item.id, item.quantity + 1)"
                                        class="h-8 w-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                    <span class="text-sm">+</span>
                                </button>
                            </div>
                            
                            <button @click="$store.cart.removeItem(item.id)"
                                    class="text-red-500 hover:text-red-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Footer -->
            <div x-show="$store.cart.items.length > 0" 
                 class="border-t border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold">Total:</span>
                    <span class="text-lg font-bold" x-text="'$' + $store.cart.total.toFixed(2)"></span>
                </div>
                
                <button class="w-full bg-black text-white py-3 rounded-md font-medium hover:bg-gray-800 mb-2">
                    Checkout
                </button>
                
                <button @click="$store.cart.close()" 
                        class="w-full border border-gray-300 text-gray-700 py-3 rounded-md font-medium hover:bg-gray-50">
                    Continue Shopping
                </button>
            </div>
        </div>
    </div>
</div>
```

## Advanced Features

### Real-time Cart Synchronization

```php
// app/Events/CartUpdated.php
<?php

namespace App\Events;

use App\Models\Cart;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Cart $cart)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('cart.' . $this->cart->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'cart' => [
                'id' => $this->cart->id,
                'total' => $this->cart->total,
                'item_count' => $this->cart->item_count,
                'updated_at' => $this->cart->updated_at,
            ]
        ];
    }
}
```

### Abandoned Cart Recovery

```php
// app/Console/Commands/ProcessAbandonedCarts.php
<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Notifications\AbandonedCartReminder;
use Illuminate\Console\Command;

class ProcessAbandonedCarts extends Command
{
    protected $signature = 'cart:process-abandoned';
    protected $description = 'Process abandoned carts and send reminder emails';

    public function handle(): void
    {
        $this->info('Processing abandoned carts...');

        // Find carts abandoned for 1 hour with items
        $abandonedCarts = Cart::whereHas('items')
            ->whereHas('user')
            ->where('updated_at', '<', now()->subHour())
            ->where('updated_at', '>', now()->subDays(7))
            ->whereDoesntHave('user.orders', function ($query) {
                $query->where('created_at', '>', now()->subDay());
            })
            ->get();

        foreach ($abandonedCarts as $cart) {
            $cart->user->notify(new AbandonedCartReminder($cart));
            $this->info("Sent reminder to user: {$cart->user->email}");
        }

        $this->info("Processed {$abandonedCarts->count()} abandoned carts");
    }
}
```

### Cart Middleware

```php
// app/Http/Middleware/CartMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $cartService = app(CartService::class);
            
            // Merge guest cart with user cart on login
            if (session()->has('guest_cart_id')) {
                $guestCart = Cart::find(session('guest_cart_id'));
                if ($guestCart) {
                    $cartService->mergeCarts($guestCart);
                    session()->forget('guest_cart_id');
                }
            }
            
            // Clean expired items
            $cartService->getCart()->clearExpiredItems();
        }

        return $next($request);
    }
}
```

### Discount System Integration

```php
// app/Services/DiscountService.php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\CartItem;

class DiscountService
{
    public function applyDiscount(Cart $cart, string $couponCode): array
    {
        $discount = Discount::where('code', $couponCode)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->first();

        if (!$discount) {
            throw new \Exception('Invalid or expired coupon code');
        }

        if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
            throw new \Exception('Coupon usage limit exceeded');
        }

        if ($discount->minimum_amount && $cart->total < $discount->minimum_amount) {
            throw new \Exception("Minimum order amount of \${$discount->minimum_amount} required");
        }

        $discountAmount = $this->calculateDiscountAmount($cart, $discount);

        return [
            'discount' => $discount,
            'discount_amount' => $discountAmount,
            'final_total' => max(0, $cart->total - $discountAmount)
        ];
    }

    protected function calculateDiscountAmount(Cart $cart, Discount $discount): float
    {
        switch ($discount->type) {
            case 'percentage':
                $amount = ($cart->total * $discount->value) / 100;
                return $discount->max_amount ? min($amount, $discount->max_amount) : $amount;
                
            case 'fixed':
                return min($discount->value, $cart->total);
                
            case 'buy_x_get_y':
                return $this->calculateBuyXGetYDiscount($cart, $discount);
                
            default:
                return 0;
        }
    }

    protected function calculateBuyXGetYDiscount(Cart $cart, Discount $discount): float
    {
        $eligibleItems = $cart->items()->whereIn('product_id', $discount->eligible_products)->get();
        
        if ($eligibleItems->sum('quantity') < $discount->buy_quantity) {
            return 0;
        }

        $freeItems = intval($eligibleItems->sum('quantity') / $discount->buy_quantity) * $discount->get_quantity;
        $cheapestItems = $eligibleItems->sortBy('unit_price')->take($freeItems);
        
        return $cheapestItems->sum('unit_price');
    }
}
```

## Testing

### Feature Tests

```php
// tests/Feature/CartTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['price' => 100.00, 'stock_quantity' => 10]);
        $this->actingAs($this->user);
        $this->cartService = app(CartService::class);
    }

    public function test_can_add_item_to_cart(): void
    {
        $response = $this->postJson('/api/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Item added to cart successfully');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
            'total_price' => 200.00
        ]);
    }

    public function test_can_update_cart_item_quantity(): void
    {
        $cartItem = $this->cartService->addItem($this->product->id, 1);

        $response = $this->putJson("/api/cart/items/{$cartItem->id}", [
            'quantity' => 3
        ]);

        $response->assertStatus(200);
        
        $this->assertEquals(3, $cartItem->fresh()->quantity);
        $this->assertEquals(300.00, $cartItem->fresh()->total_price);
    }

    public function test_can_remove_item_from_cart(): void
    {
        $cartItem = $this->cartService->addItem($this->product->id, 1);

        $response = $this->deleteJson("/api/cart/items/{$cartItem->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_cannot_add_more_items_than_stock(): void
    {
        $response = $this->postJson('/api/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 15 // More than available stock (10)
        ]);

        $response->assertStatus(400)
                 ->assertJsonPath('error', 'Insufficient stock. Only 10 items available.');
    }

    public function test_cart_validates_items_correctly(): void
    {
        $cartItem = $this->cartService->addItem($this->product->id, 5);
        
        // Reduce stock to simulate shortage
        $this->product->update(['stock_quantity' => 3]);

        $response = $this->getJson('/api/cart/validate');

        $response->assertStatus(200)
                 ->assertJsonPath('valid', false)
                 ->assertJsonPath('issues.0.type', 'insufficient_stock')
                 ->assertJsonPath('issues.0.available_quantity', 3);
    }

    public function test_guest_cart_merges_with_user_cart_on_login(): void
    {
        // Act as guest first
        auth()->logout();
        
        $guestCartItem = $this->cartService->addItem($this->product->id, 2);
        $guestCart = $guestCartItem->cart;

        // Login as user
        $this->actingAs($this->user);
        
        // Add item to user cart
        $userCartItem = $this->cartService->addItem($this->product->id, 1);
        
        // Merge guest cart
        $this->cartService->mergeCarts($guestCart);

        // Verify merged quantity
        $userCart = $this->cartService->getCart();
        $mergedItem = $userCart->items()->where('product_id', $this->product->id)->first();
        
        $this->assertEquals(3, $mergedItem->quantity); // 2 + 1
        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
    }
}
```

### Unit Tests

```php
// tests/Unit/CartServiceTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected ProductVariant $variant;
    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['price' => 50.00]);
        $this->variant = ProductVariant::factory()->create([
            'product_id' => $this->product->id,
            'price_adjustment' => 10.00
        ]);
        
        $this->actingAs($this->user);
        $this->cartService = app(CartService::class);
    }

    public function test_calculates_correct_price_with_variant(): void
    {
        $cartItem = $this->cartService->addItem($this->product->id, 1, $this->variant->id);

        $this->assertEquals(60.00, $cartItem->unit_price); // 50 + 10
        $this->assertEquals(60.00, $cartItem->total_price);
    }

    public function test_creates_product_snapshot(): void
    {
        $cartItem = $this->cartService->addItem($this->product->id, 1, $this->variant->id);

        $snapshot = $cartItem->product_snapshot;
        
        $this->assertEquals($this->product->name, $snapshot['name']);
        $this->assertEquals($this->variant->name, $snapshot['variant_name']);
        $this->assertEquals($this->variant->attributes, $snapshot['variant_attributes']);
        $this->assertArrayHasKey('captured_at', $snapshot);
    }

    public function test_updates_existing_item_when_adding_same_product(): void
    {
        $this->cartService->addItem($this->product->id, 1);
        $this->cartService->addItem($this->product->id, 2);

        $cart = $this->cartService->getCart();
        
        $this->assertEquals(1, $cart->items->count());
        $this->assertEquals(3, $cart->items->first()->quantity);
    }

    public function test_validates_stock_availability(): void
    {
        $this->product->update(['stock_quantity' => 5]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock. Only 5 items available.');
        
        $this->cartService->addItem($this->product->id, 10);
    }
}
```

## Performance Optimization

### Database Indexing

```php
// database/migrations/add_cart_indexes.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at']);
            $table->index(['session_id', 'updated_at']);
            $table->index('expires_at');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_id', 'variant_id']);
            $table->index(['product_id', 'updated_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'stock_quantity']);
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'updated_at']);
            $table->dropIndex(['session_id', 'updated_at']);
            $table->dropIndex(['expires_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id', 'product_id', 'variant_id']);
            $table->dropIndex(['product_id', 'updated_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'stock_quantity']);
        });
    }
};
```

### Caching Strategy

```php
// app/Services/CachedCartService.php
<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CachedCartService extends CartService
{
    protected function getCartCacheKey(): string
    {
        return Auth::check() 
            ? "cart:user:" . Auth::id()
            : "cart:session:" . session()->getId();
    }

    public function getCart(): Cart
    {
        $cacheKey = $this->getCartCacheKey();
        
        return Cache::remember($cacheKey, 300, function () {
            return parent::getCart();
        });
    }

    protected function broadcastCartUpdate(): void
    {
        // Clear cache after updates
        Cache::forget($this->getCartCacheKey());
        
        parent::broadcastCartUpdate();
    }

    public function preloadCartData(): void
    {
        // Preload frequently accessed cart data
        $cart = $this->getCart();
        
        Cache::put($this->getCartCacheKey() . ':summary', [
            'total' => $cart->total,
            'item_count' => $cart->item_count,
            'updated_at' => $cart->updated_at
        ], 300);
    }
}
```

### Queue Jobs for Heavy Operations

```php
// app/Jobs/ProcessCartValidation.php
<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCartValidation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Cart $cart)
    {
    }

    public function handle(CartService $cartService): void
    {
        $issues = $cartService->validateCartItems();
        
        if (!empty($issues)) {
            // Notify user about cart issues
            $this->cart->user?->notify(new CartValidationIssues($issues));
        }
    }
}
```

## API Routes

```php
// routes/api.php
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'show']);
    Route::post('/items', [CartController::class, 'addItem']);
    Route::put('/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/items/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/clear', [CartController::class, 'clear']);
    Route::get('/validate', [CartController::class, 'validate']);
    Route::post('/sync-prices', [CartController::class, 'syncPrices']);
});
```

## Frontend Enhancements

### Mobile-Optimized Cart

```css
/* resources/css/cart.css */
@media (max-width: 768px) {
    .cart-drawer {
        width: 100vw;
        max-width: none;
    }
    
    .cart-item {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .cart-item-image {
        width: 4rem;
        height: 4rem;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .quantity-button {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        background: white;
        touch-action: manipulation;
    }
    
    .quantity-button:active {
        transform: scale(0.95);
        background: #f3f4f6;
    }
}

/* Cart animations */
@keyframes slideInFromRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutToRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.cart-enter {
    animation: slideInFromRight 0.3s ease-out forwards;
}

.cart-leave {
    animation: slideOutToRight 0.2s ease-in forwards;
}

/* Loading states */
.cart-loading {
    opacity: 0.6;
    pointer-events: none;
}

.cart-loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Success animations */
.item-added {
    animation: pulse 0.5s ease-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
```

## Deployment Considerations

### Environment Configuration

```bash
# .env additions for cart system
CART_SESSION_LIFETIME=10080  # 7 days in minutes
CART_USER_LIFETIME=43200     # 30 days in minutes
CART_CLEANUP_FREQUENCY=daily
CART_ENABLE_BROADCASTING=true
CART_CACHE_TTL=300          # 5 minutes

# Broadcasting configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=your_cluster
```

### Scheduled Tasks

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Clean up expired carts
    $schedule->call(function () {
        Cart::where('expires_at', '<', now())->delete();
    })->daily();
    
    // Process abandoned cart reminders
    $schedule->command('cart:process-abandoned')->hourly();
    
    // Sync cart prices with current product prices
    $schedule->call(function () {
        Cart::whereHas('items')->chunk(100, function ($carts) {
            foreach ($carts as $cart) {
                app(CartService::class)->syncPrices();
            }
        });
    })->daily();
}
```

This comprehensive guide provides a robust, modern cart system for your Laravel 12 ecommerce application. The system includes persistent storage, real-time updates, mobile optimization, and extensive testing coverage. It's designed to handle high traffic and provide an excellent user experience across all devices.