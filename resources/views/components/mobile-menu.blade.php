<div class="mobile-menu" id="mobile-menu">
    @php
        $isAdminSession = auth('admin')->check();
        $socialLinks = array_filter([
            ['label' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'url' => $storeSettings['facebook_url'] ?? ''],
            ['label' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'url' => $storeSettings['pinterest_url'] ?? ''],
            ['label' => 'Instagram', 'icon' => 'fab fa-instagram', 'url' => $storeSettings['instagram_url'] ?? ''],
            ['label' => 'Twitter', 'icon' => 'fab fa-twitter', 'url' => $storeSettings['twitter_url'] ?? ''],
            ['label' => 'TikTok', 'icon' => 'fab fa-tiktok', 'url' => $storeSettings['tiktok_url'] ?? ''],
        ], fn ($link) => filled($link['url']));
    @endphp

    <div class="mobile-menu-header">
        <div class="urbanist-logo"><img src="{{asset("assets/images/logo/logo-dark.webp")}}" alt="logo"></div>
        <button class="mobile-menu-close" id="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="mobile-menu-nav">
        <a href="{{ route('home') }}" class="active">Home</a>
        <a href="{{ route('about') }}">About</a>
        <a href="{{ route('services') }}">Services</a>
        <a href="{{ route('offer') }}">Offer</a>
        <a href="{{ route('shop') }}">Shop</a>
        <a href="{{ route('news') }}">Blog</a>
        @if ($isAdminSession)
            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
        @elseif (auth('web')->guest())
            <a href="{{ route('login') }}">Account</a>
        @endif
    </div>

    <div class="mobile-menu-contact">
        <p>
            Call Us: {{ $storeSettings['phone_number'] }}
        </p>

        <p>Email: {{ $storeSettings['contact_email'] }}</p>

        @if ($socialLinks)
            <div class="socials">
                @foreach ($socialLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }}">
                        <i class="{{ $link['icon'] }}"></i>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
