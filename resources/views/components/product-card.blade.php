
@props([
    'product',
    'productId',
    'image',
    'description',
    'title',
    'newPrice',
    'oldPrice' => null,
    'discount' => null,
    'link' => '#',
    'inStock' => true,
    'category' => null,
    'size' => null,
])

@inject('cartService', 'App\Services\CartService')


@php
    $user = auth()->user();
    $isInWishlist = $user && $user->isInWishlist($productId);
@endphp

{{-- @if ($inStock) --}}
    <div class="product-card">
        <div class="product-relative-parent">
            <a href="{{ $link }}" class="product-image-con">
                <img class="product-image" src="/storage/{{ $image }}" alt="{{ $title }}">
            </a>

            @if(!empty($discount))
                <div class="discount-badge">
                    <i class="fas fa-tag"></i>
                    {{ $discount }}%
                </div>
            @endif

            <div class="product-right-overlay">
                <button type="button"
                    class="wishlist-toggle-btn"
                    data-product-id="{{ $productId }}" 
                    data-initial-state="{{ $isInWishlist ? '1' : '0' }}" 
                    data-auth="{{ auth()->check() ? '1' : '0' }}"
                    title="Add to Wishlist">
                    <i class="{{ $isInWishlist ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart' }}"></i>
                </button>
                <a href="{{ $link }}" title="view"><i class="fa-solid fa-images"></i></a>
            </div>

            @php
                $cartItemForThisProduct = \App\Models\CartItem::where('product_id', $product->id)
                    ->where(function($query) {
                        $query->where('session_id', session()->getId())
                            ->orWhere('user_id', auth()->id());
                    })->first();
            @endphp

            <x-add-to-cart 
                {{-- :product="$product"
                :inCart="$cartService->isInCart($product->id)" --}}
                :product="$product"
                :cartItem="$cartItemForThisProduct ?? null"
                :inCart="$cartService->isInCart($product->id)"
                {{-- :product="$product"
                :cartItem="$cartItems[$product->id] ?? null"
                :inCart="isset($cartItems[$product->id])" --}}
            />
        </div>

        <div class="product-content">
            <a href="{{ $link }}" class="product-title"><h3>{{ $title }}</h3></a>
            <p class="product-description">{{Str::limit($description, 50)}}</p>
            <div class="product-rating">
                @for($i = 0; $i < 5; $i++)
                    <i class="far fa-star"></i>
                @endfor
            </div>
            <div class="product-price">
                <em class="new-price">${{ number_format($newPrice, 2) }}</em>
                @if(!empty($oldPrice))
                    <em class="old-price">${{ number_format($oldPrice, 2) }}</em>
                @endif

                <div class="stock-badge {{ $inStock ? 'in-stock' : 'sold-out' }}">
                    {{ $inStock ? 'In Stock' : 'Sold Out' }}
                </div>
            </div>
        </div>
    </div>
{{-- @endif --}}
