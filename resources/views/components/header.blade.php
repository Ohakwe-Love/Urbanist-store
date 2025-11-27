<header id="header">
    <div class="container main-header">
        <button class="mobile-menu-trigger">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 4H3V6H17V4ZM13 11H3V13H13V11ZM17 18H3V20H17V18ZM22.0104 8.81412L20.5962 7.3999L16 11.9961L20.5962 16.5923L22.0104 15.1781L18.8284 11.9961L22.0104 8.81412Z"></path></svg>
        </button>

        <!-- navigation -->
        <nav>
            <ul>
                <li><a href="{{ route('home') }}">home</a></li>
                <li><a href="{{ route('about') }}">about</a></li>
                {{-- <li><a href="{{ route('offer') }}">offer</a></li> --}}
                <li><a href="{{ route('shop') }}">shop</a></li>
                <li><a href="{{ route('contact') }}">contact</a></li>
                <li><a href="{{ route('news') }}">news</a></li>
            </ul>
        </nav>

        <!-- logo -->
        <a href="{{ route('home') }}" class="urbanist-logo"><img src="{{asset('assets/images/logo/logo-light.webp')}}" alt=""></a>

        <div class="user-actions">
            <span id="search-trigger" class="search-trigger user-action-tag user-action-tag-icon" title="Search">
                <i class="fas fa-search"></i>
                <span class="user-action-tag-text">search</span>
            </span>
            
            <a href="{{route('wishlist')}}" class="user-action-tag" title="Wishlist">
                <span class="wishlist-icon user-action-tag-icon">
                    <i class="fa-regular fa-heart"></i>
                    <div class="wishlist-count">
                        @auth
                            {{ auth()->user()->wishlists()->count() }}
                        @else
                            0
                        @endauth
                    </div>
                </span>
                <span class="user-action-tag-text">Wishlist</span>
            </a>
            
            <x-cart-icon  />

            @auth
                <a href="{{route('dashboard')}}" title="Account" class="user-action-tag user-action-tag-icon">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="user-action-tag-text">Dashboard</span>
                </a>
            @else
                <a href="{{route('login')}}" title="Account" class="user-action-tag user-action-tag-icon">
                    <i class="fa-regular fa-user"></i>
                    <span class="user-action-tag-text">Account</span>
                </a>
            @endauth
                
        </div>
    </div>
</header>