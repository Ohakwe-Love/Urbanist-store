<x-layout>
    <!-- hero -->
    <section class="hero-container">
        <!-- Slide 1 -->
        <div class="hero-slide slide1 active">
            <div class="slide-content">
                <h1 class="slide-title">Sleeper Sofas</h1>
                <p class="slide-subtitle">New arrivals with comfort and style for your living space</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>
            </div>
        </div>
        
        <!-- Slide 2 -->
        <div class="hero-slide slide2">
            <div class="slide-content">
                <h1 class="slide-title">Fabric Sofas</h1>
                <p class="slide-subtitle">Fabric sofas for stylish living rooms</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>
            </div>
        </div>
        
        <!-- Slide 3 -->
        <div class="hero-slide slide3">
            <div class="slide-content">
                <h1 class="slide-title">Arm Chair</h1>
                <p class="slide-subtitle">Create your perfect sanctuary with our exclusive collection</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>
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
            <img src="{{asset('assets/images/grid-show/grid-show-1.webp')}}" alt="grid-show-col-1">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-2.webp')}}" alt="grid-show-col-1">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-3.webp')}}" alt="grid-show-col-1">
            <a href="{{route('shop')}}" class="shop-the-look-btn">Shop the look</a>
        </div>
        <div class="grid-show-col">
            <img src="{{asset('assets/images/grid-show/grid-show-4.webp')}}" alt="grid-show-col-1">
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
            <h2>featured collections</h2>
            <p>Shop our best selling collections for a range of styles loved by you.</p>
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
                <h2>New arrivals</h2>
                <p>Discover the latest trends and styles in home decor.</p>
                <a href="{{route('shop')}}" class="shop-btn">Shop Now</a>   
            </div>
        </div>
        <div class="new-arrivals-grid-col">
            <div class="new-arrivals-grid-col-img"><img src="{{asset('assets/images/new-arrivals/new-2.webp')}}" alt=""></div>
            <div class="new-arrivals-grid-col-text">
                <h2>Top trending</h2>
                <p>Explore our top trending products that are loved by our customers.</p>
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
