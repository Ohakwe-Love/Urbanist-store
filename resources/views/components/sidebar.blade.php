@props([
    'categories' => [],
])

<div class="sidebar">
    <h2 class="sidebar-title">Shop by</h2>

    <!-- Categories Filter -->
    <div class="filter-section">
        <div class="filter-title">
            <span>Categories</span>
            <i class="fa-solid fa-angle-down"></i>
        </div>
        <div class="filter-content">
            <div class="filter-item">
                <a href="{{ route('shop') }}" 
                   class="{{ !request()->hasAny(['category', 'availability', 'size', 'min_price', 'max_price']) ? 'active' : '' }}">
                    All Products
                </a>
            </div>
            @foreach($categories as $category)
                <div class="filter-item">
                    <a href="{{ route('shop', ['category' => $category]) }}"
                       class="{{ request('category') == $category ? 'active' : '' }}">
                        {{ ucfirst($category) }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Availability Filter -->
    <div class="filter-section">
        <div class="filter-title">
            <span>Availability</span>
            <i class="fa-solid fa-angle-down"></i>
        </div>
        <div class="filter-content">
            <div class="filter-item">
                <a href="{{ route('shop', ['availability' => 'in_stock']) }}"
                   class="{{ request('availability') == 'in_stock' ? 'active' : '' }}">
                    In stock
                </a>
            </div>
            <div class="filter-item">
                <a href="{{ route('shop', ['availability' => 'sold_out']) }}"
                   class="{{ request('availability') == 'sold_out' ? 'active' : '' }}">
                    Sold out
                </a>
            </div>
        </div>
    </div>

    <!-- Price Filter -->
    <div class="filter-section">
        <div class="filter-title">
            <span>Price Range</span>
            <i class="fa-solid fa-angle-down"></i>
        </div>
        <div class="filter-content">
            <div class="price-range">
                <div class="price-inputs">
                    <input type="number" id="min-price-input" class="min-price-input" 
                           placeholder="Min" min="0" value="{{ request('min_price', 0) }}">
                    <span>-</span>
                    <input type="number" id="max-price-input" class="max-price-input" 
                           placeholder="Max" min="0" value="{{ request('max_price', 1000) }}">
                </div>
                <input type="range" min="0" max="10000" value="{{ request('max_price', 1000) }}" 
                       class="price-slider" id="price-slider">
                <p>Price - $ <span class="price-value">{{ request('max_price', 1000) }}</span></p>
                <button type="button" id="apply-price-filter" class="apply-btn">Apply</button>
            </div>
        </div>
    </div>

    <!-- Size Filter -->
    <div class="filter-section">
        <div class="filter-title">
            <span>Size</span>
            <i class="fa-solid fa-angle-down"></i>
        </div>
        <div class="filter-content">
            <div class="size-options">
                @foreach(['small', 'medium', 'large', 'extra large'] as $size)
                    <div class="filter-item">
                        <a href="{{ route('shop', ['size' => $size]) }}"
                           class="{{ request('size') == $size ? 'active' : '' }}">
                            {{ ucfirst($size) }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Clear Filters -->
    {{-- @if(request()->hasAny(['category', 'availability', 'size', 'min_price', 'max_price']))
        <div class="filter-section">
            <a href="{{ route('shop') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-times"></i> Clear All Filters
            </a>
        </div>
    @endif --}}
</div>