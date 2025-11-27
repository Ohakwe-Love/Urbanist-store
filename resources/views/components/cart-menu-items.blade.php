<div class="cart-items">
    @if(count($cartItems) == 0)
        {{-- Empty cart state --}}
        <div class="empty-cart" id="empty-cart">
            <div class="empty-cart-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 64 64" fill="none">
                    <!-- Face Circle -->
                    <circle cx="32" cy="32" r="30" fill="none" stroke="#0d2235" stroke-width="2" />
                    <!-- Eyes -->
                    <circle cx="22" cy="24" r="4" fill="#0d2235" />
                    <circle cx="42" cy="24" r="4" fill="#0d2235" />
                    <!-- Sad Mouth -->
                    <path d="M20 44 Q32 34 44 44" stroke="#0d2235" stroke-width="3" fill="none" stroke-linecap="round" />
                </svg>
            </div>
            <h3>Your cart is empty</h3>
            <p>Add some products to get started!</p>
            <a href="{{ route('shop') }}" class="continue-shopping-btn cart-buttons">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="cart-content" id="cart-content">
            {{-- Cart has items --}}
            @foreach ($cartItems as $cartItem)
                <div class="cart-item" data-item-id="{{ $cartItem->id }}">
                    <div class="cart-item-image">
                        @if ($cartItem->product->image_url)
                            <img src="{{ asset('storage/' . $cartItem->product->image_url) }}"
                                alt="{{ $cartItem->product->title }}">
                        @else
                            <img src="{{ asset('assets/images/new-arrivals/new-1.webp') }}" alt="Default Image">
                        @endif
                    </div>

                    <div class="cart-item-details">
                        {{-- Item name --}}
                        <h3>{{ $cartItem->product->title }}</h3>

                        {{-- Item price --}}
                        <div class="cart-item-price">
                            ${{ number_format($cartItem->price, 2) }}
                        </div>

                        {{-- Item quantity controls --}}
                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus-btn" data-action="update-qty"
                                data-cart-item-id="{{ $cartItem->id }}" data-quantity="{{ $cartItem->quantity - 1 }}"
                                data-stock="{{ $cartItem->product->stock_quantity }}">-</button>

                            <span class="quantity-display">{{ $cartItem->quantity }}</span>

                            <button class="quantity-btn plus-btn" data-action="update-qty"
                                data-cart-item-id="{{ $cartItem->id }}" data-quantity="{{ $cartItem->quantity + 1 }}"
                                data-stock="{{ $cartItem->product->stock_quantity }}">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Remove item button --}}
                    <button class="remove-item remove-btn" type="button" title="Remove Item"
                        data-cart-item-id="{{ $cartItem->id }}" data-product-id="{{ $cartItem->product_id }}"
                        data-action="remove-item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if(count($cartItems) > 0)
    <div class="cart-menu-footer">
        <div class="cart-shipping">
            <span>Shipping:</span>
            <span>Free</span>
        </div>
        <div class="cart-total">
            <span>Total:</span>
            <span id="cart-total">${{ number_format($cartTotal, 2) }}</span>
        </div>
        <div class="cart-buttons-container">
            <form class="clear-cart-form" action="{{ route('cart.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="clear-cart-btn cart-buttons" id="clear-cart">Clear Cart</button>
            </form>

            <a href="#" class="cart-buttons checkout-btn">Checkout</a>
        </div>
    </div>
@endif