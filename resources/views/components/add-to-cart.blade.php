{{-- @props(['product']);
@props([
    'product',
    'quantity' => 1,
    'options' => [],
    'class' => '',
    'text' => 'Add to Cart'
])

<div class="product-bottom-overlay add-to-cart-container" data-product-id="{{ $product->id }}">
    <button 
        type="button"
        class="add-to-cart-btn {{ $class }}"
        data-product-id="{{ $product->id }}"
        data-quantity="{{ $quantity }}"
        data-options="{{ json_encode($options) }}"
        {{ $attributes }}
    >
        <span class="btn-text">{{ $text }}</span>
        <span class="btn-loading" style="display: none;">
            <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a10 10 0 0 1 10 10"></path>
            </svg>
        </span>
    </button>
</div> --}}

{{-- @props([
    'product',
    'quantity' => 1,
    'options' => [],
    'class' => '',
    'inCart' => false,
    'text' => null
])

@php
    $buttonText = $text ?? ($inCart ? 'Remove from Cart' : 'Add to Cart');
@endphp

<div class="product-bottom-overlay add-to-cart-container" data-product-id="{{ $product->id }}">
    <button 
        type="button"
        class="add-to-cart-btn {{ $class }} {{ $inCart ? 'in-cart' : '' }}"
        data-product-id="{{ $product->id }}"
        data-quantity="{{ $quantity }}"
        data-options="{{ json_encode($options) }}"
        onclick="window.CartManager.{{ $inCart ? 'removeFromCart' : 'handleAddToCart' }}(this)"
        {{ $attributes }}
    >
        <span class="btn-text">{{ $buttonText }}</span>
        <span class="btn-loading" style="display: none;">
            <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a10 10 0 0 1 10 10"></path>
            </svg>
        </span>
    </button>
</div> --}}

@props([
    'product',
    'quantity' => 1,
    'options' => [],
    'class' => '',
    'inCart' => false
])

@php
    // Check if product is in cart
    $isInCart = $inCart || (function() use ($product) {
        return \App\Models\CartItem::where('product_id', $product->id)
            ->where(function($query) {
                return $query->where('session_id', session()->getId())
                    ->orWhere('user_id', auth()->id());
            })->exists();
    })();

    $buttonText = $isInCart ? 'Remove from Cart' : 'Add to Cart';
    $buttonClass = 'add-to-cart-btn ' . $class . ($isInCart ? ' in-cart' : '');
@endphp

<div class="product-bottom-overlay add-to-cart-container">
    <button 
        type="button"
        class="{{ $buttonClass }}"
        data-product-id="{{ $product->id }}"
        data-cart-item-id="{{ $cartItem->id ?? '' }}"
        data-quantity="{{ $quantity }}"
        data-options="{{ json_encode($options) }}"
        data-in-cart="{{ $isInCart ? 'true' : 'false' }}"
        {{ $attributes }}
    >
        <span class="btn-text">{{ $buttonText }}</span>
        <span class="btn-loading" style="display: none;">
            <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a10 10 0 0 1 10 10"></path>
            </svg>
        </span>
    </button>
</div>