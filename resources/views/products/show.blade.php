<x-layout>
    {{-- page title --}}
    <x-slot name="title"></x-slot>

    {{-- style --}}
    @push ('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/product.css') }}?v={{ time() }}">
    @endpush

    @php
        $productId = $product->id;
        $user = auth()->user();
        $isInWishlist = $user && $user->isInWishlist($productId);
    @endphp

    <div class="singleProductContainer">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('shop') }}">Shop</a>
            <span>/</span>
            {{ $product->slug }}
        </div>

        <div class="product-main">
            <div class="product-images">
                <div class="main-image">
                    <img src="{{ asset( $product->image_url) }}" alt="{{ $product->title }}">
                    <button type="button"
                    class="wishlist-toggle-btn"
                    data-product-id="{{ $productId }}" 
                    data-initial-state="{{ $isInWishlist ? '1' : '0' }}" 
                    data-auth="{{ auth()->check() ? '1' : '0' }}"
                    title="Add to Wishlist">
                        <i class="{{ $isInWishlist ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart' }}"></i>
                    </button>
                </div>

                <div class="image-thumbnails">
                    <div class="thumbnail active" data-image="/api/placeholder/600/500">
                        <img src="{{asset('storage/' . $product->image_url)}}" alt="">
                    </div>
                    <div class="thumbnail" data-image="/api/placeholder/600/500?text=Red">
                        <img src="{{asset('assets/images/new-arrivals/new-11.webp')}}" alt="">
                    </div>
                    <div class="thumbnail" data-image="/api/placeholder/600/500?text=Green">
                        <img src="{{asset('assets/images/new-arrivals/new-7.webp')}}" alt="">
                    </div>
                    <div class="thumbnail" data-image="/api/placeholder/600/500?text=Gray">
                        <img src="{{asset('assets/images/new-arrivals/new-4.webp')}}" alt="">
                    </div>
                    <div class="thumbnail" data-image="/api/placeholder/600/500?text=Black">
                        <img src="{{asset('assets/images/new-arrivals/new-5.webp')}}" alt="">
                    </div>
                </div>
            </div>

            <div class="product-info">
                <h1 class="product-title">Diamond Halo Stud Chair</h1>
                <div class="rating-container">
                    <div class="stars-con">
                        <span class="star"><i class="fa-regular fa-star"></i></span>
                        <span class="star"><i class="fa-regular fa-star"></i></span>
                        <span class="star"><i class="fa-regular fa-star"></i></span>
                        <span class="star"><i class="fa-regular fa-star"></i></span>
                        <span class="star"><i class="fa-regular fa-star"></i></span>
                    </div>
                    <a href="#reviews" class="review-link"> Reviews</a>
                </div>
                <div class="price">
                    {{$product->sale_price}}
                    <span class="old-price">{{$product->price}}</span>
                </div>
                <div class="about-product">
                    <div class="availability">
                        <span>Availability:</span> 
                        
                        @if ($product->stock_quantity > 0)
                            <div class="stock-status">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                In Stock
                            </div>
                        @else 
                            <div class="stock-status sold-out">  
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Sold out
                            </div>
                        @endif
                    </div>
                    <div class="product-tags">
                        @foreach(explode(',', $product->category) as $cat)
                            <div class="tag">{{ trim($cat) }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="quantity-container">
                    <div class="quantity-selector">
                        <button class="quantity-btn" id="decrease-qty">-</button>
                        <input type="number" min="1" value="1" class="quantity-input" id="qty-input">
                        <button class="quantity-btn" id="increase-qty">+</button>
                    </div>
                    <button class="add-to-cart-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        Add to Cart
                    </button>
                </div>
                <div class="action-buttons">
                    <button class="buy-now-btn">
                        Buy Now
                    </button>
                </div>
            </div>
        </div>

        <div class="tabs">
            <div class="tab-navigation">
                <div class="tab-item active" data-tab="description">Description</div>
                <div class="tab-item" data-tab="specifications">Specifications</div>
                <div class="tab-item" data-tab="reviews">Reviews (0)</div>
                {{-- <div class="tab-item" data-tab="delivery">Shipping & Returns</div> --}}
            </div>

            <div class="tab-content">
                <div class="tab-panel active" id="description">
                    <p>
                        {{$product->description}}
                    </p>
                    <div class="delivery-info">
                        <div class="delivery-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Orders ship within 3 to 5 business days</span>
                        </div>
                        <div class="delivery-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                            <span>Free delivery on orders over $200</span>
                        </div>
                        <div class="delivery-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>Delivery available to all locations in the world</span>
                        </div>
                    </div>
                </div>

                <div class="tab-panel specifications" id="specifications">
                    @forelse($product->specifications as $spec)
                        <div class="spec-row">
                            <div class="spec-label">{{ $spec->label }}</div>
                            <div class="spec-value">{{ $spec->value }}</div>
                        </div>
                    @empty
                        <div class="spec-row">
                            <div class="spec-label">No specifications available.</div>
                        </div>
                    @endforelse
                    {{-- <div class="spec-row">
                        <div class="spec-label"></div>
                        <div class="spec-value">W30" x D32" x H42"</div>
                    </div>
                    
                    <div class="spec-row">
                        <div class="spec-label">Weight</div>
                        <div class="spec-value">35 lbs</div>
                    </div>
                    
                    <div class="spec-row">
                        <div class="spec-label">Materials</div>
                        <div class="spec-value">
                            <ul class="spec-list">
                                <li>Frame: Solid hardwood</li>
                                <li>Upholstery: Premium polyester blend fabric</li>
                                <li>Legs: Solid beech wood</li>
                                <li>Padding: High-density foam</li>
                            </ul>
                        </div>
                    </div> --}}
                    
                    {{-- <div class="spec-row">
                        <div class="spec-label">Available Colors</div>
                        <div class="spec-value">
                            <div class="color-option">
                                <span class="color-swatch color-blue"></span>Blue
                            </div>
                            <div class="color-option">
                                <span class="color-swatch color-red"></span>Red
                            </div>
                            <div class="color-option">
                                <span class="color-swatch color-green"></span>Green
                            </div>
                            <div class="color-option">
                                <span class="color-swatch color-gray"></span>Gray
                            </div>
                            <div class="color-option">
                                <span class="color-swatch color-black"></span>Black
                            </div>
                        </div>
                    </div> --}}
                    
                    {{-- <div class="spec-row">
                        <div class="spec-label">Assembly</div>
                        <div class="spec-value">Minimal assembly required, legs need to be attached</div>
                    </div>
                    
                    <div class="spec-row">
                        <div class="spec-label">Origin</div>
                        <div class="spec-value">
                            <div class="origin-flag">Made in USA
                            </div>
                        </div>
                    </div>
                    
                    <div class="spec-row">
                        <div class="spec-label">Warranty</div>
                        <div class="spec-value">
                            <span class="warranty-badge">3-year limited warranty</span>
                        </div>
                    </div> --}}
                </div>

                <div class="tab-panel" id="reviews">
                    <div class="rating-summary">
                        <div class="rating-number">0.0</div>
                        <div class="stars-con">
                            <span class="star"><i class="fa-regular fa-star"></i></span>
                            <span class="star"><i class="fa-regular fa-star"></i></span>
                            <span class="star"><i class="fa-regular fa-star"></i></span>
                            <span class="star"><i class="fa-regular fa-star"></i></span>
                            <span class="star"><i class="fa-regular fa-star"></i></span>
                        </div>

                        <div class="rating-count">Based on 0 reviews</div>
                        
                        <div class="rating-breakdown">
                            <div class="rating-bar">
                                <div class="rating-label">5 <span class="label-stars"><i class="fa-regular fa-star"></i></span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="rating-percent">0%</div>
                            </div>
                        
                            <div class="rating-bar">
                                <div class="rating-label">4 <span class="label-stars"><i class="fa-regular fa-star"></i></span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="rating-percent">0%</div>
                            </div>
                        
                            <div class="rating-bar">
                                <div class="rating-label">3 <span class="label-stars"><i class="fa-regular fa-star"></i></span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="rating-percent">0%</div>
                            </div>
                        
                            <div class="rating-bar">
                                <div class="rating-label">2 <span class="label-stars"><i class="fa-regular fa-star"></i></span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="rating-percent">0%</div>
                            </div>
                        
                            <div class="rating-bar">
                                <div class="rating-label">1 <span class="label-stars"><i class="fa-regular fa-star"></i></span></div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="rating-percent">0%</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="review-prompt">
                        <div class="prompt-text">
                        Have you purchased this product? We'd love to hear your thoughts!
                        </div>
                        <button class="review-btn">Write a Review</button>
                    </div>
                    {{-- <p>Customer reviews will appear here. This section would typically include a list of customer reviews, ratings, and the ability to submit a new review.</p> --}}
                </div>

                {{-- <div class="tab-panel" id="delivery">
                    <p><strong>Shipping Policy:</strong></p>
                    <p>We offer free standard shipping on all orders over $500. For orders under $500, a flat shipping rate of $49 applies. Orders typically ship within 3-5 business days.</p>
                    <p><strong>Delivery Information:</strong></p>
                    <p>Your furniture will be delivered to your door by our premium delivery service. For an additional fee, we offer white glove delivery service, which includes in-home delivery, assembly, and packaging removal.</p>
                    <p><strong>Returns:</strong></p>
                    <p>We stand behind the quality of our products. If you're not completely satisfied with your purchase, you may return it within 30 days of delivery for a full refund or exchange. The item must be in its original condition and packaging. Return shipping fees may apply.</p>
                </div> --}}
            </div>
        </div>
    </div>

    @push ('scripts')
        <script>
            // Thumbnail gallery functionality
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('main-product-image');
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                // Update active state
                thumbnails.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                
                // Update main image
                const imageSrc = this.querySelector('img').src;
                mainImage.src = imageSrc;
                });
            });
            
            // Quantity selector functionality
            const decreaseBtn = document.getElementById('decrease-qty');
            const increaseBtn = document.getElementById('increase-qty');
            const qtyInput = document.getElementById('qty-input');
            
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(qtyInput.value);
                if (currentValue > 1) {
                    qtyInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(qtyInput.value);
                qtyInput.value = currentValue + 1;
            });
            
            // Tab functionality
            const tabItems = document.querySelectorAll('.tab-item');
            const tabPanels = document.querySelectorAll('.tab-panel');
            
            tabItems.forEach(item => {
                item.addEventListener('click', function() {
                // Update active tab
                tabItems.forEach(tab => tab.classList.remove('active'));
                this.classList.add('active');
                
                // Show active panel
                const tabId = this.getAttribute('data-tab');
                tabPanels.forEach(panel => panel.classList.remove('active'));
                document.getElementById(tabId).classList.add('active');
                });
            })
        </script>
    @endpush
</x-layout>