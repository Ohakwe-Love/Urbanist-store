@php
    $user = auth()->user();
    $wishlists = $user->wishlistProducts()->get();
@endphp

<x-dashboard-layout :user="$user">
    <x-slot name="title">{{ucfirst($user->username)}}  | Wishlists</x-slot>

    <div class="dashboard-header">
        <div>
            <h1>Hey, {{ ucfirst(Auth::user()->username) }}!</h1>
            <p class="welcome-subtext">Products in your wishlists.</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

           
    @forelse($wishlists as $product)
        <div class="wishlists-row">
            <div class="wishlist-item">
                <a href="{{ route('show', $product->slug) }}" class="wishlist-item-image">
                    <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->title }}">
                </a>

                <div class="wishlist-item-details">
                    <a class="wishlist-item-title" href="{{route('show', $product->slug)}}">
                        <h3>{{ $product->title }}</h3>
                    </a>

                    <p class="wishlist-item-description">{{ $product->description }}</p>

                    <div class="wishlist-item-price">
                        <p>
                            Amount:
                            <span class="new-price">${{ number_format($product->new_price ?? $product->price, 2) }}</span>
                        </p>
                    </div>

                    <div class="wishlist-item-actions">
                        <button type="button" class="add-to-cart-btn" data-product-id="{{ $product->id }}">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button type="button" class="remove-from-wishlist-btn" data-product-id="{{ $product->id }}">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="no-wishlists-container">
            <p class="no-wishlists">You have no wishlists yet.</p>
        </div>
    @endforelse

    @push('scripts')
        <script src="{{asset('assets/js/wishlist.js')}}"></script>
    @endpush
</x-dashboard-layout>