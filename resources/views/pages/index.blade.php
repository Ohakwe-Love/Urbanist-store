<x-layout>
    @php
        $defaultBlocks = \App\Models\ContentBlock::defaults();
        $homeBanner = ($contentBlocks['home_banner']['is_active'] ?? true) ? ($contentBlocks['home_banner'] ?? $defaultBlocks['home_banner']) : $defaultBlocks['home_banner'];
        $promoBlock = ($contentBlocks['promotional_section']['is_active'] ?? true) ? ($contentBlocks['promotional_section'] ?? $defaultBlocks['promotional_section']) : $defaultBlocks['promotional_section'];
        $featuredBlock = ($contentBlocks['featured_collections']['is_active'] ?? true) ? ($contentBlocks['featured_collections'] ?? $defaultBlocks['featured_collections']) : $defaultBlocks['featured_collections'];
    @endphp

    <!-- hero -->
    <section class="hero-container">
        <!-- Slide 1 -->
        <div class="hero-slide slide1 active">
            <div class="slide-content">
                <h1 class="slide-title">{{ $homeBanner['title'] }}</h1>
                <p class="slide-subtitle">{{ $homeBanner['content'] }}</p>
                <a href="{{route('shop')}}" class="shop-btn">{{ $homeBanner['meta']['cta_label'] ?? 'Shop Now' }}</a>
            </div>
        </div>
        
        <!-- Slide 2 -->
        <div class="hero-slide slide2">
            <div class="slide-content">
                <h1 class="slide-title">{{ $homeBanner['meta']['slide_two_title'] ?? 'Fabric Sofas' }}</h1>
                <p class="slide-subtitle">{{ $homeBanner['meta']['slide_two_content'] ?? 'Fabric sofas for stylish living rooms' }}</p>
                <a href="{{route('shop')}}" class="shop-btn">{{ $homeBanner['meta']['cta_label'] ?? 'Shop Now' }}</a>
            </div>
        </div>
        
        <!-- Slide 3 -->
        <div class="hero-slide slide3">
            <div class="slide-content">
                <h1 class="slide-title">{{ $homeBanner['meta']['slide_three_title'] ?? 'Arm Chair' }}</h1>
                <p class="slide-subtitle">{{ $homeBanner['meta']['slide_three_content'] ?? 'Create your perfect sanctuary with our exclusive collection' }}</p>
                <a href="{{route('shop')}}" class="shop-btn">{{ $homeBanner['meta']['cta_label'] ?? 'Shop Now' }}</a>
            </div>
        </div>

        <!-- Pagination Dots -->
        <div class="pagination">
            <div class="dot active" data-slide="0"></div>
            <div class="dot" data-slide="1"></div>
            <div class="dot" data-slide="2"></div>
        </div>
    </section>
    <!-- hero end -->

    <!-- grid-show -->
    <div class="grid-show">
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-1.webp')}}" alt="Layered neutral living room">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-2.webp')}}" alt="Statement furniture composition">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-3.webp')}}" alt="Accent decor styling">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-4.webp')}}" alt="Comfortable bedroom furniture">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
    </div>
    <!-- grid-show end -->

    <!-- services -->
    <x-services-grid />
    <!-- services end -->

    <!-- featured collections -->
    <section class="featured-collections">
        <div class="section-header">
            <h2>{{ $featuredBlock['title'] }}</h2>
            <p>{{ $featuredBlock['content'] }}</p>
        </div>

        <div class="featured-collections-grid">
            <div class="featured-collections-grid-col">
                <div class="featured-collections-grid-col-img"><img src="assets/images/collections/featured-1.webp" alt=""></div>
                <a href="{{route('shop')}}"><h3>Lamp decor</h3></a>
                <p>Beside lamps, ceiling lamps, and decorative wall lamps</p>
            </div>
            <div class="featured-collections-grid-col">
                <div class="featured-collections-grid-col-img"><img src="assets/images/collections/featured-2.webp" alt=""></div>
                <a href="{{route('shop')}}"><h3>plant pots</h3></a>
                <p>Decorative plant pots, a touch of green space for your sweet home.</p>
            </div>
            <div class="featured-collections-grid-col">
                <div class="featured-collections-grid-col-img"><img src="assets/images/collections/featured-3.webp" alt=""></div>
                <a href="{{route('shop')}}"><h3>Lamp decor</h3></a>
                <p>Modern dining chairs & Kitchen chairs to complete your mealtime setup.</p>
            </div>
            <div class="featured-collections-grid-col">
                <div class="featured-collections-grid-col-img"><img src="assets/images/collections/featured-4.webp" alt=""></div>
                <a href="{{route('shop')}}"><h3>home decor</h3></a>
                <p>Beautify your home with our selection of decor  and home goods</p>
            </div>
            <div class="featured-collections-grid-col">
                <div class="featured-collections-grid-col-img"><img src="assets/images/collections/featured-5.webp" alt=""></div>
                <a href="{{route('shop')}}"><h3>Leather sofa</h3></a>
                <p>Check out leather sofa featuring several colors, styles, and designs.</p>
            </div>
        </div>
    </section>

    <!-- new arrivals -->
    <section class="new-arrivals-grid">
        <div class="new-arrivals-grid-col">
            <div class="new-arrivals-grid-col-img"><img src="{{asset('assets/images/new-arrivals/new-1.webp')}}" alt=""></div>
            <div class="new-arrivals-grid-col-text">
                <h2>{{ $promoBlock['title'] }}</h2>
                <p>{{ $promoBlock['content'] }}</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>   
            </div>
        </div>
        <div class="new-arrivals-grid-col">
            <div class="new-arrivals-grid-col-img"><img src="{{asset('assets/images/new-arrivals/new-2.webp')}}" alt=""></div>
            <div class="new-arrivals-grid-col-text">
                <h2>{{ $promoBlock['meta']['secondary_title'] ?? 'Top trending' }}</h2>
                <p>{{ $promoBlock['meta']['secondary_content'] ?? 'Explore our top trending products that are loved by our customers.' }}</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>   
            </div>
        </div>
    </section>

    <!-- new for you -->
    <section class="new-for-you">
        <div class="section-header">
            <h2>New for you</h2>
            <p>Discover the latest trends and styles in home decor.</p>
        </div>

        <div class="products-row-container">
            <div class="products-row">
                @forelse($latestNewProducts as $product)
                    <x-product-card
                        :product="$product"
                        :productId="$product->id"
                        :title="$product->title"
                        :image="$product->image_url"
                        :description="$product->description"
                        :newPrice="$product->sale_price ?? $product->price"
                        :oldPrice="$product->sale_price ? $product->price : null"
                        :discount="$product->discount"
                        :link="route('show', $product->slug)"
                        :inStock="$product->stock_quantity > 0"
                        :category="$product->category"
                        :size="$product->size"
                    />
                @empty
                    <p>No new products found.</p>
                @endforelse
            </div>
        </div>
    </section>
    <!-- new for you end -->

    <!-- urbanist popup -->
    {{-- <x-urbanist-popup /> --}}
    <!-- urbanist-popup end -->

    <!-- faqs -->
    <x-faqs />
    <!-- faqs end -->
</x-layout>
