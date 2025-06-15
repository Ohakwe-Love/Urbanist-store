<div class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu-header">
        <div class="urbanist-logo"><img src="{{asset("assets/images/logo/logo-dark.webp")}}" alt="logo"></div>
        <button class="mobile-menu-close" id="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="mobile-menu-nav">
        <a href="{{ route('home') }}" class="active">Home</a>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('offer') }}">Offer</a>
        <a href="{{ route('shop') }}">Shop</a>
        <a href="{{ route('news') }}">Blog</a>
        @guest
            <a href="{{ route('login') }}">Account</a>
        @endguest
    </div>

    <!-- Contact us -->
    <div class="mobile-menu-contact">
        <p>
            Call Us: (012)-345-67890
        </p>

        <p>Email: support@domain.com</p>

        <div class="socials">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-pinterest-p"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>
</div>