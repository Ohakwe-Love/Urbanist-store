```php
<?php

// 1. Cart Service Class (app/Services/CartService.php)
namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    private $sessionKey = 'shopping_cart';

    public function getCart()
    {
        return Session::get($this->sessionKey, []);
    }

    public function addToCart($productId, $name, $price, $quantity = 1, $image = null, $attributes = [])
    {
        $cart = $this->getCart();
        $itemKey = $this->generateItemKey($productId, $attributes);

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $quantity;
        } else {
            $cart[$itemKey] = [
                'id' => $productId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
                'attributes' => $attributes,
                'item_key' => $itemKey
            ];
        }

        Session::put($this->sessionKey, $cart);
        return $cart[$itemKey];
    }

    public function updateQuantity($itemKey, $quantity)
    {
        $cart = $this->getCart();
        
        if (isset($cart[$itemKey])) {
            if ($quantity <= 0) {
                unset($cart[$itemKey]);
            } else {
                $cart[$itemKey]['quantity'] = $quantity;
            }
            Session::put($this->sessionKey, $cart);
        }

        return $cart;
    }

    public function removeFromCart($itemKey)
    {
        $cart = $this->getCart();
        unset($cart[$itemKey]);
        Session::put($this->sessionKey, $cart);
        return $cart;
    }

    public function clearCart()
    {
        Session::forget($this->sessionKey);
    }

    public function getCartCount()
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    public function getCartTotal()
    {
        $cart = $this->getCart();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }

    private function generateItemKey($productId, $attributes = [])
    {
        $key = $productId;
        if (!empty($attributes)) {
            ksort($attributes);
            $key .= '_' . md5(serialize($attributes));
        }
        return $key;
    }
}

// 2. Cart Controller (app/Http/Controllers/CartController.php)
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'integer|min:1',
            'image' => 'nullable|string',
            'attributes' => 'nullable|array'
        ]);

        $item = $this->cartService->addToCart(
            $request->product_id,
            $request->name,
            $request->price,
            $request->quantity ?? 1,
            $request->image,
            $request->attributes ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'item' => $item,
            'cart_count' => $this->cartService->getCartCount(),
            'cart_total' => $this->cartService->getCartTotal()
        ]);
    }

    public function get()
    {
        $cart = $this->cartService->getCart();
        
        return response()->json([
            'cart' => $cart,
            'cart_count' => $this->cartService->getCartCount(),
            'cart_total' => $this->cartService->getCartTotal()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = $this->cartService->updateQuantity($request->item_key, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart' => $cart,
            'cart_count' => $this->cartService->getCartCount(),
            'cart_total' => $this->cartService->getCartTotal()
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string'
        ]);

        $cart = $this->cartService->removeFromCart($request->item_key);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart' => $cart,
            'cart_count' => $this->cartService->getCartCount(),
            'cart_total' => $this->cartService->getCartTotal()
        ]);
    }

    public function clear()
    {
        $this->cartService->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    }
}

// 3. Routes (routes/web.php or routes/api.php)
use App\Http\Controllers\CartController;

Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/get', [CartController::class, 'get'])->name('cart.get');
    Route::patch('/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// 4. Service Provider Registration (app/Providers/AppServiceProvider.php)
// Add this to the register() method:
public function register()
{
    $this->app->singleton(CartService::class);
}

// 5. View Composer for Cart Data (app/Providers/AppServiceProvider.php)
// Add this to the boot() method:
use Illuminate\Support\Facades\View;
use App\Services\CartService;

public function boot()
{
    View::composer('*', function ($view) {
        $cartService = app(CartService::class);
        $view->with([
            'cartCount' => $cartService->getCartCount(),
            'cartTotal' => $cartService->getCartTotal()
        ]);
    });
}

// 6. JavaScript for Frontend Integration
/*
// Add this to your main JavaScript file or blade template

class CartManager {
    constructor() {
        this.init();
    }

    init() {
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart, .add-to-cart *')) {
                e.preventDefault();
                const button = e.target.closest('.add-to-cart');
                this.addToCart(button);
            }
        });

        // Update quantity buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.cart-quantity-update')) {
                this.updateQuantity(e.target);
            }
        });

        // Remove item buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.cart-remove-item')) {
                this.removeItem(e.target);
            }
        });

        // Load cart on page load
        this.loadCart();
    }

    async addToCart(button) {
        const productData = {
            product_id: button.dataset.productId,
            name: button.dataset.name,
            price: parseFloat(button.dataset.price),
            quantity: parseInt(button.dataset.quantity || 1),
            image: button.dataset.image,
            attributes: button.dataset.attributes ? JSON.parse(button.dataset.attributes) : {}
        };

        try {
            button.disabled = true;
            button.textContent = 'Adding...';

            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(productData)
            });

            const data = await response.json();

            if (data.success) {
                this.updateCartUI(data);
                this.showNotification('Item added to cart!', 'success');
            } else {
                this.showNotification('Failed to add item to cart', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('An error occurred', 'error');
        } finally {
            button.disabled = false;
            button.textContent = 'Add to Cart';
        }
    }

    async updateQuantity(element) {
        const itemKey = element.dataset.itemKey;
        const quantity = parseInt(element.value);

        try {
            const response = await fetch('/cart/update', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ item_key: itemKey, quantity })
            });

            const data = await response.json();
            if (data.success) {
                this.updateCartUI(data);
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }

    async removeItem(element) {
        const itemKey = element.dataset.itemKey;

        try {
            const response = await fetch('/cart/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ item_key: itemKey })
            });

            const data = await response.json();
            if (data.success) {
                this.updateCartUI(data);
                this.showNotification('Item removed from cart', 'success');
            }
        } catch (error) {
            console.error('Error removing item:', error);
        }
    }

    async loadCart() {
        try {
            const response = await fetch('/cart/get');
            const data = await response.json();
            this.updateCartUI(data);
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    updateCartUI(data) {
        // Update cart count in header
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(el => el.textContent = data.cart_count);

        // Update cart total
        const cartTotalElements = document.querySelectorAll('.cart-total');
        cartTotalElements.forEach(el => el.textContent = '$' + data.cart_total.toFixed(2));

        // Update cart items in slide menu
        this.renderCartItems(data.cart);
    }

    renderCartItems(cart) {
        const cartContainer = document.querySelector('.cart-items-container');
        if (!cartContainer) return;

        if (Object.keys(cart).length === 0) {
            cartContainer.innerHTML = '<p class="text-center text-gray-500">Your cart is empty</p>';
            return;
        }

        let html = '';
        Object.values(cart).forEach(item => {
            html += `
                <div class="cart-item flex items-center justify-between p-4 border-b">
                    <div class="flex items-center space-x-3">
                        ${item.image ? `<img src="${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded">` : ''}
                        <div>
                            <h4 class="font-medium">${item.name}</h4>
                            <p class="text-sm text-gray-500">$${item.price}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="number" value="${item.quantity}" min="1" 
                               class="cart-quantity-update w-16 text-center border rounded" 
                               data-item-key="${item.item_key}">
                        <button class="cart-remove-item text-red-500 hover:text-red-700" 
                                data-item-key="${item.item_key}">×</button>
                    </div>
                </div>
            `;
        });

        cartContainer.innerHTML = html;
    }

    showNotification(message, type = 'info') {
        // Implement your notification system here
        console.log(`${type}: ${message}`);
    }
}

// Initialize cart manager
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
});
*/