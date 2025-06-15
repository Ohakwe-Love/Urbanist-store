```html
<div class="cart-menu" id="cart-menu">
    <div class="cart-menu-header">
        <h2>Your Shopping Cart</h2>
        <button class="cart-menu-close" id="cart-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="cart-items">
        @if($cartItems->count() > 0)
            @foreach($cartItems as $item)
                <div class="cart-item" data-product-id="{{ $item->product_id }}">
                    <div class="cart-item-image">
                        @if($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                 alt="{{ $item->product->name }}">
                        @else
                            <img src="{{ asset('assets/images/placeholder.jpg') }}" 
                                 alt="{{ $item->product->name }}">
                        @endif
                    </div>
                    <div class="cart-item-details">
                        <h3>{{ $item->product->name }}</h3>
                        <div class="cart-item-price">${{ number_format($item->price, 2) }}</div>
                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus" 
                                    data-product-id="{{ $item->product_id }}" 
                                    data-action="decrease">-</button>
                            <input type="number" 
                                   class="quantity-input"
                                   value="{{ $item->quantity }}" 
                                   min="1" 
                                   max="99"
                                   data-product-id="{{ $item->product_id }}">
                            <button class="quantity-btn plus" 
                                    data-product-id="{{ $item->product_id }}" 
                                    data-action="increase">+</button>
                        </div>
                    </div>
                    <button class="remove-item" 
                            title="Remove Item"
                            data-product-id="{{ $item->product_id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>
    
    @if($cartItems->count() > 0)
        <div class="cart-menu-footer">
            <div class="cart-subtotal">
                <span>Subtotal:</span>
                <span>${{ number_format($cartTotal, 2) }}</span>
            </div>
            <div class="cart-shipping">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="cart-total">
                <span>Total:</span>
                <span id="cart-total">${{ number_format($cartTotal, 2) }}</span>
            </div>
            <div class="cart-buttons">
                <a href="{{ route('cart.index') }}" class="btn btn-outline">View Cart</a>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary">Checkout</a>
            </div>
            <div class="cart-clear">
                <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="clear-cart-btn" 
                            onclick="return confirm('Are you sure you want to clear the cart?')">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

<style>
/* Additional styles for empty cart and clear cart functionality */
.empty-cart {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-light);
}

.empty-cart-icon {
    font-size: 48px;
    color: var(--color-border-gray);
    margin-bottom: 20px;
}

.empty-cart h3 {
    font-size: 18px;
    margin-bottom: 10px;
    color: var(--text-color);
}

.empty-cart p {
    margin-bottom: 20px;
    font-size: 14px;
}

.cart-clear {
    margin-top: 15px;
    text-align: center;
    border-top: 1px solid var(--color-border-gray);
    padding-top: 15px;
}

.clear-cart-btn {
    background: transparent;
    border: none;
    color: var(--color-danger);
    font-size: 12px;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 3px;
    transition: var(--transition);
}

.clear-cart-btn:hover {
    background-color: rgba(255, 107, 107, 0.1);
}

/* Ensure the existing CSS variables are defined */
:root {
    --white: #ffffff;
    --color-white: #ffffff;
    --color-light-gray: #f8f9fa;
    --color-border-gray: #e9ecef;
    --text-color: #333333;
    --text-light: #666666;
    --primary-color: #007bff;
    --primary-color-hover: #0056b3;
    --color-danger: #dc3545;
    --transition: all 0.3s ease;
}
</style>