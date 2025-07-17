@props(['cartCount' => 0])


<span class="user-action-tag cart-btn cart-toggle-btn" 
    title="Cart"
    data-cart-count="{{ $cartData['count'] }}"
    onclick="toggleCartMenu()">
    <span class="cart-icon user-action-tag-icon">
        <i class="fas fa-shopping-cart"></i>

        <div class="cart-count" id="cart-count">{{ $cartData['count'] }}</div>
    </span>
    <span class="user-action-tag-text">Cart</span>
</span>