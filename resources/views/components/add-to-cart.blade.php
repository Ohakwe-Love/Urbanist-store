@props([
    'product',
    'stockQuantity' => null,
    'quantity' => 1,
    'options' => [],
    'class' => '',
    'inCart' => false,
    'cartItemId' => null,
])

@php
    $buttonText = $inCart ? 'Remove from Cart' : 'Add to Cart';
    $buttonClass = 'add-to-cart-btn ' . $class . ($inCart ? ' in-cart' : '');
@endphp

<div class="product-bottom-overlay add-to-cart-container">
    <button 
        type="button"
        class="{{ $buttonClass }}"
        data-stock="{{ $stockQuantity }}"
        data-product-id="{{ $product->id }}"
        data-cart-item-id="{{ $cartItemId ?? '' }}"
        data-quantity="{{ $quantity }}"
        data-options="{{ json_encode($options) }}"
        data-in-cart="{{ $inCart ? 'true' : 'false' }}"
        data-add-text="Add to Cart"
        data-remove-text="Remove from Cart"
        {{ $attributes }}
    >
        <span class="btn-text">{{ $buttonText }}</span>
        <span class="btn-loading" style="display: none;">
            <svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2"></circle>
                <path d="M12 2a10 10 0 0 1 10 10" stroke-width="2" stroke-linecap="round"></path>
            </svg>
        </span>
    </button>
</div>