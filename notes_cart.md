```php
<?php

// 1. Migration for Cart table
// database/migrations/create_cart_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['session_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart');
    }
};

// 2. Cart Model
// app/Models/Cart.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    
    protected $fillable = [
        'session_id',
        'user_id', 
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }
}

// 3. Product Model (add cart relationship)
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer'
    ];

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function isInStock($quantity = 1)
    {
        return $this->stock_quantity >= $quantity;
    }
}

// 4. Cart Service
// app/Services/CartService.php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function addToCart(Product $product, int $quantity = 1)
    {
        if (!$product->isInStock($quantity)) {
            throw new \Exception('Insufficient stock');
        }

        $cartItem = $this->findCartItem($product);

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            if (!$product->isInStock($newQuantity)) {
                throw new \Exception('Insufficient stock for requested quantity');
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'session_id' => $this->getSessionId(),
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price
            ]);
        }

        return true;
    }

    public function updateQuantity(Product $product, int $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeFromCart($product);
        }

        if (!$product->isInStock($quantity)) {
            throw new \Exception('Insufficient stock');
        }

        $cartItem = $this->findCartItem($product);
        
        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
            return true;
        }

        return false;
    }

    public function removeFromCart(Product $product)
    {
        $cartItem = $this->findCartItem($product);
        
        if ($cartItem) {
            $cartItem->delete();
            return true;
        }

        return false;
    }

    public function getCartItems()
    {
        return Cart::with('product')
            ->where(function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', $this->getSessionId());
                }
            })
            ->get();
    }

    public function getCartTotal()
    {
        return $this->getCartItems()->sum('subtotal');
    }

    public function getCartCount()
    {
        return $this->getCartItems()->sum('quantity');
    }

    public function clearCart()
    {
        Cart::where(function ($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', $this->getSessionId());
            }
        })->delete();
    }

    public function mergeGuestCart()
    {
        if (Auth::check() && Session::has('cart_session_id')) {
            $guestItems = Cart::where('session_id', Session::get('cart_session_id'))->get();
            
            foreach ($guestItems as $guestItem) {
                $existingItem = Cart::where('user_id', Auth::id())
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $guestItem->quantity
                    ]);
                } else {
                    $guestItem->update([
                        'user_id' => Auth::id(),
                        'session_id' => null
                    ]);
                }
            }

            // Clean up any remaining guest items
            Cart::where('session_id', Session::get('cart_session_id'))->delete();
            Session::forget('cart_session_id');
        }
    }

    private function findCartItem(Product $product)
    {
        return Cart::where('product_id', $product->id)
            ->where(function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->where('session_id', $this->getSessionId());
                }
            })
            ->first();
    }

    private function getSessionId()
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }
        
        return Session::get('cart_session_id');
    }
}

// 5. Cart Controller
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();
        $cartTotal = $this->cartService->getCartTotal();
        
        return view('cart.index', compact('cartItems', 'cartTotal'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->cartService->addToCart($product, $request->quantity);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added to cart',
                    'cartCount' => $this->cartService->getCartCount()
                ]);
            }

            return redirect()->back()->with('success', 'Product added to cart');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $this->cartService->updateQuantity($product, $request->quantity);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cartTotal' => $this->cartService->getCartTotal(),
                    'cartCount' => $this->cartService->getCartCount()
                ]);
            }

            return redirect()->back()->with('success', 'Cart updated');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function remove(Product $product)
    {
        $this->cartService->removeFromCart($product);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartTotal' => $this->cartService->getCartTotal(),
                'cartCount' => $this->cartService->getCartCount()
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart');
    }

    public function clear()
    {
        $this->cartService->clearCart();
        
        return redirect()->back()->with('success', 'Cart cleared');
    }

    public function count()
    {
        return response()->json([
            'count' => $this->cartService->getCartCount()
        ]);
    }
}

// 6. Routes
// routes/web.php

use App\Http\Controllers\CartController;

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// 7. Event Listener for User Login (merge guest cart)
// app/Listeners/MergeGuestCart.php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeGuestCart
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Login $event)
    {
        $this->cartService->mergeGuestCart();
    }
}

// 8. Register the listener in EventServiceProvider
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\MergeGuestCart;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            MergeGuestCart::class,
        ],
    ];
}

// 9. View Composer for Cart Count
// app/Http/ViewComposers/CartComposer.php

namespace App\Http\ViewComposers;

use App\Services\CartService;
use Illuminate\View\View;

class CartComposer
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {
        $view->with('cartCount', $this->cartService->getCartCount());
    }
}

// 10. Register View Composer in AppServiceProvider
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Http\ViewComposers\CartComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', CartComposer::class);
    }
}

<!-- cart views -->
{{-- 1. Cart Index View --}}
{{-- resources/views/cart/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

    @if($cartItems->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4">Product</th>
                            <th class="text-left py-3 px-4">Price</th>
                            <th class="text-left py-3 px-4">Quantity</th>
                            <th class="text-left py-3 px-4">Subtotal</th>
                            <th class="text-left py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                            <tr class="border-b cart-item" data-product-id="{{ $item->product_id }}">
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded mr-4">
                                        @endif
                                        <div>
                                            <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                            <p class="text-gray-600 text-sm">{{ Str::limit($item->product->description, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">${{ number_format($item->price, 2) }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <button type="button" class="quantity-btn minus-btn bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded-l" 
                                                data-product-id="{{ $item->product_id }}" 
                                                data-action="decrease">-</button>
                                        <input type="number" 
                                               class="quantity-input w-16 text-center border-t border-b border-gray-200 py-1" 
                                               value="{{ $item->quantity }}" 
                                               min="1" 
                                               data-product-id="{{ $item->product_id }}">
                                        <button type="button" class="quantity-btn plus-btn bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded-r" 
                                                data-product-id="{{ $item->product_id }}" 
                                                data-action="increase">+</button>
                                    </div>
                                </td>
                                <td class="py-4 px-4 subtotal">${{ number_format($item->subtotal, 2) }}</td>
                                <td class="py-4 px-4">
                                    <button type="button" class="remove-btn text-red-600 hover:text-red-800" 
                                            data-product-id="{{ $item->product_id }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <div>
                    <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" 
                                onclick="return confirm('Are you sure you want to clear the cart?')">
                            Clear Cart
                        </button>
                    </form>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold">Total: $<span id="cart-total">{{ number_format($cartTotal, 2) }}</span></div>
                    <div class="mt-4">
                        <a href="#" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293a1 1 0 00-.707 1.707v1a1 1 0 001 1h12m-6 0a2 2 0 11-4 0m8 0a2 2 0 11-4 0"></path>
            </svg>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-4">Add some products to get started!</p>
            <a href="{{ route('products.index') }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const action = this.dataset.action;
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            let quantity = parseInt(input.value);

            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease' && quantity > 1) {
                quantity--;
            }

            input.value = quantity;
            updateCart(productId, quantity);
        });
    });

    // Direct quantity input
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            
            if (quantity > 0) {
                updateCart(productId, quantity);
            }
        });
    });

    // Remove buttons
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            removeFromCart(productId);
        });
    });

    function updateCart(productId, quantity) {
        fetch(`/cart/update/${productId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update subtotal for the row
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                const price = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('$', ''));
                const subtotal = price * quantity;
                row.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
                
                // Update total
                document.getElementById('cart-total').textContent = parseFloat(data.cartTotal).toFixed(2);
                
                // Update cart count in navbar
                updateNavCartCount(data.cartCount);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function removeFromCart(productId) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch(`/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row
                    document.querySelector(`tr[data-product-id="${productId}"]`).remove();
                    
                    // Update total
                    document.getElementById('cart-total').textContent = parseFloat(data.cartTotal).toFixed(2);
                    
                    // Update cart count in navbar
                    updateNavCartCount(data.cartCount);
                    
                    // Check if cart is empty
                    if (data.cartCount === 0) {
                        location.reload(); // Reload to show empty cart message
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    }

    function updateNavCartCount(count) {
        const cartCountElement = document.getElementById('nav-cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }
});
</script>
@endsection

{{-- 2. Add to Cart Button Component --}}
{{-- resources/views/components/add-to-cart.blade.php --}}

@props(['product', 'quantity' => 1, 'showQuantity' => true])

<div class="add-to-cart-form" data-product-id="{{ $product->id }}">
    @if($showQuantity)
        <div class="flex items-center mb-4">
            <label for="quantity-{{ $product->id }}" class="mr-2">Quantity:</label>
            <div class="flex items-center">
                <button type="button" class="quantity-btn bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded-l" data-action="decrease">-</button>
                <input type="number" 
                       id="quantity-{{ $product->id }}" 
                       name="quantity" 
                       value="{{ $quantity }}" 
                       min="1" 
                       max="{{ $product->stock_quantity }}"
                       class="quantity-input w-16 text-center border-t border-b border-gray-200 py-1">
                <button type="button" class="quantity-btn bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded-r" data-action="increase">+</button>
            </div>
        </div>
    @endif

    <button type="button" 
            class="add-to-cart-btn bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" 
            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
        @if($product->stock_quantity <= 0)
            Out of Stock
        @else
            Add to Cart
        @endif
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity buttons for add to cart component
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        const productId = form.dataset.productId;
        const quantityInput = form.querySelector('.quantity-input');
        const addButton = form.querySelector('.add-to-cart-btn');
        
        // Quantity buttons
        form.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                let quantity = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.getAttribute('max'));

                if (action === 'increase' && quantity < maxStock) {
                    quantity++;
                } else if (action === 'decrease' && quantity > 1) {
                    quantity--;
                }

                quantityInput.value = quantity;
            });
        });

        // Add to cart button
        addButton.addEventListener('click', function() {
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            // Disable button temporarily
            this.disabled = true;
            this.textContent = 'Adding...';
            
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.textContent = 'Added!';
                    this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    this.classList.add('bg-green-600');
                    
                    // Update cart count in navbar
                    updateNavCartCount(data.cartCount);
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.textContent = 'Add to Cart';
                        this.classList.remove('bg-green-600');
                        this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        this.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message);
                    this.textContent = 'Add to Cart';
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                this.textContent = 'Add to Cart';
                this.disabled = false;
            });
        });
    });

    function updateNavCartCount(count) {
        const cartCountElement = document.getElementById('nav-cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }
});
</script>

```php
{{-- 3. Navigation Cart Icon --}}
{{-- resources/views/components/cart-icon.blade.php --}}

<a href="{{ route('cart.index') }}" class="relative inline-flex items-center p-2 text-gray-600 hover:text-gray-900">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293a1 1 0 00-.707 1.707v1a1 1 0 001 1h12m-6 0a2 2 0 11-4 0m8 0a2 2 0 11-4 0"></path>
    </svg>
    @if($cartCount > 0)
        <span id="nav-cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
            {{ $cartCount }}
        </span>
    @else
        <span id="nav-cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
            0
        </span>
    @endif
</a>

{{-- 4. Product Card Example with Add to Cart --}}
{{-- resources/views/products/card.blade.php --}}

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($product->image)
        <img src="{{ asset('storage/' . $product->image) }}" 
             alt="{{ $product->name }}" 
             class="w-full h-48 object-cover">
    @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-500">No Image</span>
        </div>
    @endif
    
    <div class="p-4">
        <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($product->description, 100) }}</p>
        <div class="flex justify-between items-center mb-4">
            <span class="text-xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
            <span class="text-sm text-gray-500">Stock: {{ $product->stock_quantity }}</span>
        </div>
        
        <x-add-to-cart :product="$product" />
    </div>
</div>
```php

```php
// {{-- 5. Products Index with Cart Integration --}}
// {{-- resources/views/products/index.blade.php --}}

// @extends('layouts.app')

// @section('title', 'Products')

// @section('content')
// <div class="container mx-auto px-4 py-8">
//     <div class="flex justify-between items-center mb-6">
//         <h1 class="text-3xl font-bold">Products</h1>
//         <x-cart-icon />
//     </div>

//     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
//         @forelse($products as $product)
//             @include('products.card', ['product' => $product])
//         @empty
//             <div class="col-span-full text-center py-8">
//                 <p class="text-gray-500">No products available.</p>
//             </div>
//         @endforelse
//     </div>

//     @if($products->hasPages())
//         <div class="mt-8">
//             {{ $products->links() }}
//         </div>
//     @endif
// </div>
// @endsection
```php


{{-- 6. Product Show Page with Add to Cart --}}
{{-- resources/views/products/show.blade.php --}}

@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full rounded-lg shadow-md">
            @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500 text-xl">No Image Available</span>
                </div>
            @endif
        </div>
        
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-6">{{ $product->description }}</p>
            
            <div class="mb-6">
                <span class="text-3xl font-bold text-blue-600">${{ number_format($product->price, 2) }}</span>
                <span class="text-gray-500 ml-4">
                    @if($product->stock_quantity > 0)
                        {{ $product->stock_quantity }} in stock
                    @else
                        Out of stock
                    @endif
                </span>
            </div>

            <x-add-to-cart :product="$product" :show-quantity="true" />
            
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-lg font-semibold mb-2">Product Details</h3>
                <ul class="text-gray-600">
                    <li>SKU: {{ $product->id }}</li>
                    <li>Category: {{ $product->category ?? 'Uncategorized' }}</li>
                    <li>Availability: {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 7. Main Layout with Cart Integration --}}
{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Laravel Cart') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                        Products
                    </a>
                    
                    <x-cart-icon />
                    
                    @auth
                        <div class="relative group">
                            <button class="text-gray-600 hover:text-gray-900">
                                {{ Auth::user()->name }}
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
    @yield('scripts')
</body>
</html>

{{-- 8. Mini Cart Dropdown Component (Optional) --}}
{{-- resources/views/components/mini-cart.blade.php --}}

<div class="relative group">
    <x-cart-icon />
    
    @if($cartCount > 0)
        <div class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
            <div class="p-4">
                <h3 class="text-lg font-semibold mb-3">Shopping Cart</h3>
                
                @php
                    $cartService = app(\App\Services\CartService::class);
                    $cartItems = $cartService->getCartItems()->take(3);
                    $cartTotal = $cartService->getCartTotal();
                @endphp
                
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-12 h-12 object-cover rounded">
                            @endif
                            <div class="flex-1">
                                <h4 class="font-medium text-sm">{{ Str::limit($item->product->name, 30) }}</h4>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <span class="text-sm font-medium">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                
                @if($cartItems->count() < $cartCount)
                    <p class="text-xs text-gray-500 mt-2">And {{ $cartCount - $cartItems->count() }} more items...</p>
                @endif
                
                <div class="border-t pt-3 mt-3">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-lg">${{ number_format($cartTotal, 2) }}</span>
                    </div>
                    
                    <div class="space-y-2">
                        <a href="{{ route('cart.index') }}" 
                           class="block w-full text-center bg-gray-200 text-gray-800 py-2 rounded hover:bg-gray-300 transition-colors">
                            View Cart
                        </a>
                        <a href="#" 
                           class="block w-full text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                           Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- enhanced js -->
// resources/js/cart.js
// Enhanced Cart Functionality with AJAX and Real-time Updates

class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.bindEvents();
        this.updateCartCount();
    }

    bindEvents() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                this.handleAddToCart(e.target.closest('.add-to-cart-form'));
            }
        });

        // Quantity update buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quantity-btn')) {
                e.preventDefault();
                this.handleQuantityChange(e.target);
            }
        });

        // Direct quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                this.handleQuantityInputChange(e.target);
            }
        });

        // Remove from cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn') || e.target.closest('.remove-btn')) {
                e.preventDefault();
                this.handleRemoveFromCart(e.target);
            }
        });

        // Clear cart confirmation
        document.addEventListener('click', (e) => {
            if (e.target.closest('form')?.action?.includes('/cart/clear')) {
                if (!confirm('Are you sure you want to clear your entire cart?')) {
                    e.preventDefault();
                }
            }
        });
    }

    async handleAddToCart(form) {
        const productId = form.dataset.productId;
        const quantityInput = form.querySelector('.quantity-input');
        const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
        const button = form.querySelector('.add-to-cart-btn');

        if (button.disabled) return;

        // Disable button and show loading state
        this.setButtonLoading(button, true);

        try {
            const response = await this.makeRequest(`/cart/add/${productId}`, 'POST', { quantity });
            
            if (response.success) {
                this.showSuccessButton(button);
                this.updateCartCount(response.cartCount);
                this.showNotification('Product added to cart!', 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showNotification('Failed to add product to cart', 'error');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    handleQuantityChange(button) {
        const form = button.closest('.add-to-cart-form') || button.closest('.cart-item');
        const productId = form.dataset.productId || button.dataset.productId;
        const action = button.dataset.action;
        const input = form.querySelector('.quantity-input') || 
                     document.querySelector(`input[data-product-id="${productId}"]`);
        
        if (!input) return;

        let quantity = parseInt(input.value);
        const maxStock = parseInt(input.getAttribute('max')) || 999;

        if (action === 'increase' && quantity < maxStock) {
            quantity++;
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        } else {
            return; // No change needed
        }

        input.value = quantity;

        // If this is in cart page, update the cart
        if (form.classList.contains('cart-item')) {
            this.updateCartQuantity(productId, quantity);
        }
    }

    handleQuantityInputChange(input) {
        const form = input.closest('.cart-item');
        if (!form) return;

        const productId = form.dataset.productId;
        const quantity = parseInt(input.value);

        if (quantity > 0) {
            this.updateCartQuantity(productId, quantity);
        }
    }

    async handleRemoveFromCart(button) {
        const productId = button.dataset.productId || 
                         button.closest('[data-product-id]').dataset.productId;

        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const response = await this.makeRequest(`/cart/remove/${productId}`, 'DELETE');
            
            if (response.success) {
                this.removeCartRow(productId);
                this.updateCartCount(response.cartCount);
                this.updateCartTotal(response.cartTotal);
                this.showNotification('Item removed from cart', 'success');
                
                // Check if cart is empty
                if (response.cartCount === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Remove from cart error:', error);
            this.showNotification('Failed to remove item from cart', 'error');
        }
    }

    async updateCartQuantity(productId, quantity) {
        try {
            const response = await this.makeRequest(`/cart/update/${productId}`, 'PATCH', { quantity });
            
            if (response.success) {
                this.updateCartRow(productId, quantity);
                this.updateCartCount(response.cartCount);
                this.updateCartTotal(response.cartTotal);
            } else {
                this.showNotification(response.message, 'error');
                // Revert the input value
                const input = document.querySelector(`input[data-product-id="${productId}"]`);
                if (input) {
                    // Get the current quantity from the server or reset to 1
                    input.value = 1;
                }
            }
        } catch (error) {
            console.error('Update cart error:', error);
            this.showNotification('Failed to update cart', 'error');
        }
    }

    updateCartRow(productId, quantity) {
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (!row) return;

        const priceElement = row.querySelector('td:nth-child(2)');
        const subtotalElement = row.querySelector('.subtotal');
        
        if (priceElement && subtotalElement) {
            const price = parseFloat(priceElement.textContent.replace('$', ''));
            const subtotal = price * quantity;
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
        }
    }

    removeCartRow(productId) {
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (row) {
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
    }

    updateCartCount(count) {
        if (count !== undefined) {
            const elements = document.querySelectorAll('#nav-cart-count, .cart-count');
            elements.forEach(element => {
                element.textContent = count;
                if (count > 0) {
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
        } else {
            // Fetch current count from server
            this.fetchCartCount();
        }
    }

    updateCartTotal(total) {
        const totalElement = document.getElementById('cart-total');
        if (totalElement && total !== undefined) {
            totalElement.textContent = parseFloat(total).toFixed(2);
        }
    }

    async fetchCartCount() {
        try {
            const response = await fetch('/cart/count');
            const data = await response.json();
            this.updateCartCount(data.count);
        } catch (error) {
            console.error('Failed to fetch cart count:', error);
        }
    }

    setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.textContent;
            button.textContent = 'Adding...';
            button.classList.add('opacity-75');
        } else {
            button.disabled = false;
            button.textContent = button.dataset.originalText || 'Add to Cart';
            button.classList.remove('opacity-75');
        }
    }

    showSuccessButton(button) {
        const originalText = button.textContent;
        const originalClasses = button.className;
        
        button.textContent = 'Added!';
        button.className = button.className.replace('bg-blue-600', 'bg-green-600')
                                         .replace('hover:bg-blue-700', 'hover:bg-green-700');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.className = originalClasses;
        }, 2000);
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
        
        // Set colors based on type
        switch (type) {
            case 'success':
                notification.className += ' bg-green-500 text-white';
                break;
            case 'error':
                notification.className += ' bg-red-500 text-white';
                break;
            default:
                notification.className += ' bg-blue-500 text-white';
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    async makeRequest(url, method, data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
}

// Enhanced Cart Features
class CartEnhancements {
    constructor() {
        this.init();
    }

    init() {
        this.addLocalStorageBackup();
        this.addKeyboardShortcuts();
        this.addCartSummarySticky();
        this.addQuantityValidation();
    }

    addLocalStorageBackup() {
        // Backup cart state to localStorage for offline resilience
        const cartData = this.getCartData();
        if (cartData.length > 0) {
            localStorage.setItem('cart_backup', JSON.stringify(cartData));
        }
    }

    addKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[type="search"]');
                if (searchInput) searchInput.focus();
            }
            
            // Escape to close modals/dropdowns
            if (e.key === 'Escape') {
                const activeDropdowns = document.querySelectorAll('.group:hover');
                activeDropdowns.forEach(dropdown => dropdown.classList.remove('hover'));
            }
        });
    }

    addCartSummarySticky() {
        const cartSummary = document.querySelector('.cart-summary');
        if (!cartSummary) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    cartSummary.classList.add('sticky', 'top-4');
                } else {
                    cartSummary.classList.remove('sticky', 'top-4');
                }
            });
        });

        observer.observe(cartSummary);
    }

    addQuantityValidation() {
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('quantity-input')) {
                const input = e.target;
                const value = parseInt(input.value);
                const min = parseInt(input.getAttribute('min')) || 1;
                const max = parseInt(input.getAttribute('max')) || 999;

                if (value < min) {
                    input.value = min;
                } else if (value > max) {
                    input.value = max;
                    this.showStockWarning(max);
                }
            }
        });
    }

    showStockWarning(maxStock) {
        const warning = document.createElement('div');
        warning.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded shadow-lg z-50';
        warning.textContent = `Only ${maxStock} items available in stock`;
        document.body.appendChild(warning);

        setTimeout(() => {
            warning.style.opacity = '0';
            setTimeout(() => document.body.removeChild(warning), 300);
        }, 3000);
    }

    getCartData() {
        const cartRows = document.querySelectorAll('.cart-item');
        return Array.from(cartRows).map(row => ({
            productId: row.dataset.productId,
            quantity: row.querySelector('.quantity-input')?.value || 1,
            price: row.querySelector('td:nth-child(2)')?.textContent.replace('$', '') || 0
        }));
    }
}

// Shopping Cart Analytics (Optional)
class CartAnalytics {
    constructor() {
        this.trackCartEvents();
    }

    trackCartEvents() {
        // Track add to cart events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn')) {
                const productId = e.target.closest('.add-to-cart-form').dataset.productId;
                this.trackEvent('add_to_cart', { product_id: productId });
            }
        });

        // Track remove from cart events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-btn')) {
                const productId = e.target.dataset.productId;
                this.trackEvent('remove_from_cart', { product_id: productId });
            }
        });
    }

    trackEvent(eventName, data) {
        // Send to analytics service (Google Analytics, etc.)
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, data);
        }
        
        // Or send to your own analytics endpoint
        // fetch('/analytics/track', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ event: eventName, data })
        // });
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
    new CartEnhancements();
    // new CartAnalytics(); // Uncomment if you want analytics
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { CartManager, CartEnhancements, CartAnalytics };
}

<!--  -->
$this->app->singleton(CartService::class);

<script src="{{ asset('js/cart.js') }}"></script>

<x-add-to-cart :product="$product" />
<x-cart-icon />