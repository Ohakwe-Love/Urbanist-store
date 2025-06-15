<div class="cart-menu" id="cart-menu">
    <div class="cart-menu-header">
        <h2>Your Shopping Cart({{$cartCount}})</h2>
        <button class="cart-menu-close" id="cart-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    @include('components.cart-menu-content')

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
                <button type="submit" class="clear-cart-btn cart-buttons">Clear Cart</button>
            </form>
            
            <a href="#" class="cart-buttons checkout-btn">Checkout</a>
        </div>
    </div>
</div>