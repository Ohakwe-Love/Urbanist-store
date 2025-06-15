@props([
    'product',
    'quantity' => 1, 
    'showQuantity' => true
])

<div class="product-bottom-overlay add-to-cart-container" data-product-id="{{ $product->id }}">
    <button type="button" class="add-to-cart-btn {{ $product->isInCart() ? 'in-cart' : '' }}{{ $product->stock_quantity <= 0 ? 'disabled' : '' }}">

        @if($product->stock_quantity <= 0)
            Sold Out
        @else
            {{ $product->isInCart() ? 'Remove Item' : 'Add to Cart' }}
        @endif
    </button>
</div>