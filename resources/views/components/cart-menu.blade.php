<div class="cart-menu" id="cart-menu">
    <div class="cart-menu-header">
        <h2>Your Shopping Cart({{$cartCount}})</h2>
        <button class="cart-menu-close cart-close" id="cart-menu-close" onclick="
        toggleCartMenu()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="cart-items">
        {{-- @if ($cartCount > 0)
            @foreach ($cartItems as $cartItem)
                <div class="cart-item">
                    <div class="cart-item-image">
                        @if ($cartItem->product->image_url)
                            <img src="{{ asset('storage/' . $cartItem->product->image_url) }}" alt="{{ $cartItem->product->name }}">
                        @else
                            <img src="{{ asset(path: 'assets/images/new-arrivals/new-1.webp') }}" alt="Default Image">
                        @endif
                    </div>
                    <div class="cart-item-details">
                        <h3>{{$cartItem->product->title}}</h3>
                        <div class="cart-item-price">
                            ${{ number_format($cartItem->price, 2) }}
                        </div>

                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus-btn" 
                                data-product-id="{{ $cartItem->product_id }}" 
                                data-action="decrease">
                                -
                            </button>
                            <input type="number" 
                                class="quantity-input"
                                value="{{ $cartItem->quantity }}" 
                                min="1" 
                                max="99"
                                data-product-id="{{ $cartItem->product_id }}"
                            >
                            <button class="quantity-btn plus-btn" 
                                data-product-id="{{ $cartItem->product_id }}" 
                                data-action="increase">
                                +
                            </button>
                        </div>
                    </div>

                    <button class="remove-item" 
                        title="Remove Item"
                        data-product-id="{{ $cartItem->product_id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 64 64" fill="none">
                        <!-- Face Circle -->
                        <circle cx="32" cy="32" r="30" fill="none" stroke="#0d2235" stroke-width="2"/>

                        <!-- Eyes -->
                        <circle cx="22" cy="24" r="4" fill="#0d2235"/>
                        <circle cx="42" cy="24" r="4" fill="#0d2235"/>

                        <!-- Sad Mouth -->
                        <path d="M20 44 Q32 34 44 44" stroke="#0d2235" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="{{ route('shop') }}" class="continue-shopping-btn cart-buttons">
                    Continue Shopping
                </a>
            </div>
        @endif --}}

        @if($cartData['isEmpty'])
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 64 64" fill="none">
                        <!-- Face Circle -->
                        <circle cx="32" cy="32" r="30" fill="none" stroke="#0d2235" stroke-width="2"/>

                        <!-- Eyes -->
                        <circle cx="22" cy="24" r="4" fill="#0d2235"/>
                        <circle cx="42" cy="24" r="4" fill="#0d2235"/>

                        <!-- Sad Mouth -->
                        <path d="M20 44 Q32 34 44 44" stroke="#0d2235" stroke-width="3" fill="none" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="{{ route('shop') }}" class="continue-shopping-btn cart-buttons">
                    Continue Shopping
                </a>
            </div>
        @else 
            @foreach ($cartData['items'] as $cartItem)
                <div class="cart-item" data-item-id="{{ $cartItem->id }}">
                    <div class="cart-item-image">
                        {{-- <img src="{{asset('assets/images/grid-show/grid-show-1.webp')}}" alt=""> --}}
                        @if ($cartItem->product->image_url)
                            <img src="{{ asset('storage/' . $cartItem->product->image_url) }}" alt="{{ $cartItem->product->name }}">
                        @else
                            <img src="{{ asset(path: 'assets/images/new-arrivals/new-1.webp') }}" alt="Default Image">
                        @endif
                    </div>
                    <div class="cart-item-details">
                        {{-- item name --}}
                        <h3>{{$cartItem->product->title}}</h3>

                        {{-- item price --}}
                        <div class="cart-item-price">
                            ${{ number_format($cartItem->price, 2) }}
                        </div>

                        {{-- item quantity --}}
                        <div class="cart-item-quantity">
                            <button class="quantity-btn minus-btn" 
                            onclick="updateQuantity({{ $cartItem->id }}, {{ $cartItem->quantity - 1 }})">-</button>
                            <span class="quantity-input">{{ $cartItem->quantity }}</span>
                            <button class="quantity-btn plus-btn" 
                            onclick="updateQuantity({{ $cartItem->id }}, {{ $cartItem->quantity + 1 }})">+</button>
                        </div>
                    </div>

                    {{-- remove item --}}
                    <button class="remove-item remove-btn" 
                        type="button"
                        title="Remove Item"
                        data-product-id="{{ $cartItem->product_id }}" 
                        onclick="removeFromCart({{ $cartItem->id }})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        @endif
    </div>

    @if(!$cartData['isEmpty'])
        <div class="cart-menu-footer">
            <div class="cart-shipping">
                <span>Shipping:</span>
                <span>Free</span>
            </div>
            <div class="cart-total">
                <span>Total:</span>
                <span id="cart-total">${{ number_format($cartData['total'], 2) }}</span>
            </div>
            <div class="cart-buttons-container">    
                <form class="clear-cart-form" action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="clear-cart-btn cart-buttons" onclick="clearCart()">Clear Cart</button>
                </form>
                
                <a href="#" class="cart-buttons checkout-btn">Checkout</a>
            </div>
        </div>
    @endif
</div>