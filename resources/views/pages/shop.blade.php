<x-layout>
    {{-- page title --}}
    <x-slot name="title">Shop | Urbanist</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}">
    @endpush

    {{-- shop container --}}
    <div class="shop-container">
        <!-- Sidebar / Filters -->
        <x-sidebar :categories="$categories"/>

        {{-- sibe bar over --}}
        <div class="sidebar-overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Shop Header -->
            <div class="shop-header">
                <div>
                    <h1 class="shop-title">Products</h1>
                    <span id="total-products">{{ $totalProducts }}</span> products found
                    {{-- @if(request()->hasAny(['category', 'availability', 'size', 'min_price', 'max_price']))
                        <span class="filter-indicator">
                            - Filtered by: 
                            @if(request('category'))
                                <span class="badge bg-primary">{{ ucfirst(request('category')) }}</span>
                            @elseif(request('availability'))
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', request('availability'))) }}</span>
                            @elseif(request('size'))
                                <span class="badge bg-success">{{ ucfirst(request('size')) }}</span>
                            @elseif(request('min_price') || request('max_price'))
                                <span class="badge bg-warning">
                                    ${{ request('min_price', 0) }} - ${{ request('max_price', '∞') }}
                                </span>
                            @endif
                        </span>
                    @endif --}}
                </div>
                <div class="view-options">
                    <div class="sort-by">
                        <span>Sort by:</span>
                        <select>
                            <option>Featured</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest First</option>
                            <option>Best Selling</option>
                        </select>
                    </div>
                    <div class="view-options-con">
                        <div class="view-option" data-view="1">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="view-option" data-view="2">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div class="view-option active" data-view="3">
                            <i class="fas fa-th"></i>
                        </div>
                        <div class="open-sidebar">
                            <i class="fas fa-bars"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- products cards --}}
            <div id="products-container">
                <x-product-row :products="$products" />
            </div>

            {{-- loading skeleton --}}
            <div id="products-skeleton">
                @for($i = 0; $i < 6; $i++)
                    <div class="product-card skeleton">
                        <div class="skeleton-img"></div>
                        <div class="skeleton-title"></div>
                        <div class="skeleton-price"></div>
                    </div>
                @endfor
            </div>

            {{-- load more btn --}}
            {{-- @if($products->count() > 0)
                <button id="load-more-btn" class="load-more-btn">Load More</button>
            @endif --}}
            @if($hasMoreProducts)
                <div class="">
                    <button id="load-more-btn" class="load-more-btn btn btn-primary" data-page="2">
                        <span class="btn-text">Load More Products</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa-solid fa-spinner fa-spin"></i> Loading...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        window.shopRoute = "{{ route('shop') }}";
    </script>

    {{-- about.js --}}
    @push('scripts')
        <script src="{{ asset('assets/js/shop.js') }}"></script>
    @endpush
</x-layout>