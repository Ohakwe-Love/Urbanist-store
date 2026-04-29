<footer>
    @php
        $socialLinks = array_filter([
            ['label' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'url' => $storeSettings['facebook_url'] ?? ''],
            ['label' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'url' => $storeSettings['pinterest_url'] ?? ''],
            ['label' => 'Instagram', 'icon' => 'fab fa-instagram', 'url' => $storeSettings['instagram_url'] ?? ''],
            ['label' => 'Twitter', 'icon' => 'fab fa-twitter', 'url' => $storeSettings['twitter_url'] ?? ''],
            ['label' => 'TikTok', 'icon' => 'fab fa-tiktok', 'url' => $storeSettings['tiktok_url'] ?? ''],
        ], fn ($link) => filled($link['url']));
    @endphp
    <div class="footer-content">
        <div class="footer-section">
            <h3>{{ $storeSettings['store_name'] }}</h3>
            <p>Modern furniture, lighting, and decor selected for calm, livable spaces.</p>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div class="contact-text-hotline">
                        <p class="hotline">HOTLINE :</p>
                        <p>{{ $storeSettings['phone_number'] }}</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p class="contact-text"><a href="mailto:{{ $storeSettings['contact_email'] }}">{{ $storeSettings['contact_email'] }}</a></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-calendar"></i>
                    <p class="contact-text">{{ $storeSettings['business_hours'] }}</p>
                </div>
            </div>
        </div>
    
        <div class="footer-section">
            <h3>Help</h3>
            <ul class="footer-links">
            <li><a href="{{ route('help-center') }}">Help Center</a></li>
            <li><a href="{{ route('how-to-order') }}">Shipping Info</a></li>
            <li><a href="{{route('returns')}}">Returns</a></li>
            <li><a href="{{route('how-to-order')}}">How To Order</a></li>
            <li><a href="{{route(name: 'cookies')}}">Cookies</a></li>
            <li><a href="{{route('policies')}}">Policies</a></li>
            </ul>
        </div>
    
        <div class="footer-section">
            <h3>Company</h3>
            <ul class="footer-links">
                <li><a href="{{route('about')}}">About Us</a></li>
                <li><a href="{{ route('services') }}">Services</a></li>
                <li><a href="{{route('offer')}}">Offers</a></li>
                <li><a href="{{route('news')}}">News Update</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
        </div>
    
        <div class="footer-section">
            <h3>Newsletter</h3>
            <p>Be the first to hear about launches, offers, and fresh pieces for the home.</p>
            <form action="{{ route('newsletter.store') }}" method="POST" class="newsletter-form">
                @csrf
                <input type="email" name="email" placeholder="Your Email Address" value="{{ old('email') }}" aria-label="Newsletter email">
                <button type="submit"><i class="fas fa-arrow-right"></i></button>
            </form>
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

    <div class="footer-bottom">
        <p>Copyright &copy; <span class="">{{date('Y')}}</span> {{ $storeSettings['store_name'] }}. All rights reserved.</p>
    </div>
</footer>
