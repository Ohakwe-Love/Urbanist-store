@props(['cartCount' => 0])


<span class="user-action-tag cart-btn" title="Cart">
    <span class="cart-icon user-action-tag-icon">
        <i class="fas fa-shopping-cart"></i>
        @if($cartCount > 0)
            <div class="cart-count" id="nav-cart-count">{{ $cartCount }}</div>
        @else
            <div class="cart-count" id="nav-cart-count">0</div>
        @endif
    </span>
    <span class="user-action-tag-text">Cart</span>
</span>