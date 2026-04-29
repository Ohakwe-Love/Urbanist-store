<x-layout>
    <x-slot name="title">Special Offers | Urbanist</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/offer.css') }}">
    @endpush

    <section class="offer-page">
        <div class="offer-hero">
            <div class="container">
                <div class="page-route"><a href="{{ route('home') }}">Home</a>&ensp;/&ensp; Offers</div>
                <div class="offer-hero-grid">
                    <div class="offer-hero-copy">
                        <span class="offer-kicker">Current campaign</span>
                        <h1>Seasonal savings for rooms that feel considered.</h1>
                        <p>Urbanist offers thoughtful pricing on furniture, lighting, and decor pieces that help you finish a room without rushing the decision. Explore current highlights, limited bundles, and customer-favorite essentials.</p>
                        <div class="offer-hero-actions">
                            <a href="{{ route('shop') }}" class="offer-primary-link">Shop featured offers</a>
                            <a href="{{ route('contact') }}" class="offer-secondary-link">Talk to a stylist</a>
                        </div>
                    </div>

                    <div class="offer-hero-panel">
                        <div class="offer-hero-stat">
                            <span>Featured event</span>
                            <strong>Up to 20% off</strong>
                            <small>Selected lounge seating, accent lighting, and finishing pieces</small>
                        </div>
                        <div class="offer-hero-stat">
                            <span>Included service</span>
                            <strong>Delivery support</strong>
                            <small>Guidance on timing, handling, and setup for qualifying furniture orders</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <section class="offer-editorial-grid">
                <article class="offer-editorial-card is-large">
                    <div class="offer-editorial-media">
                        <img src="{{ asset('assets/images/new-arrivals/new-10.webp') }}" alt="Curated lounge furniture">
                    </div>
                    <div class="offer-editorial-copy">
                        <span class="offer-kicker">Living room edit</span>
                        <h2>Lounge pieces with more breathing room in the budget.</h2>
                        <p>Discover softer silhouettes, textured upholstery, and accent tables chosen to help anchor a calm, high-comfort living space.</p>
                    </div>
                </article>

                <article class="offer-editorial-card">
                    <div class="offer-editorial-media">
                        <img src="{{ asset('assets/images/collections/featured-4.webp') }}" alt="Decor accessories">
                    </div>
                    <div class="offer-editorial-copy">
                        <span class="offer-kicker">Decor accents</span>
                        <h3>Small updates, sharper mood.</h3>
                        <p>Vases, mirrors, and tabletop details that make a room feel intentionally finished.</p>
                    </div>
                </article>

                <article class="offer-editorial-card">
                    <div class="offer-editorial-media">
                        <img src="{{ asset('assets/images/new-arrivals/new-7.webp') }}" alt="Bedroom furniture">
                    </div>
                    <div class="offer-editorial-copy">
                        <span class="offer-kicker">Bedroom calm</span>
                        <h3>Quiet pieces for better resets.</h3>
                        <p>Storage, lighting, and upholstered forms chosen for spaces that need less noise and more ease.</p>
                    </div>
                </article>
            </section>

            <section class="offer-bands">
                <article class="offer-band">
                    <span class="offer-band-label">Bundle value</span>
                    <h3>Pair seating with side tables and save more on the set.</h3>
                    <p>Ideal for customers finishing a room in one pass rather than purchasing item by item.</p>
                </article>
                <article class="offer-band">
                    <span class="offer-band-label">New customer perk</span>
                    <h3>Newsletter subscribers still get first access to launch-week offers.</h3>
                    <p>Join the list for early notice on collections, quieter markdowns, and limited inventory releases.</p>
                </article>
                <article class="offer-band">
                    <span class="offer-band-label">Support promise</span>
                    <h3>Need help deciding what works together? We can guide the selection.</h3>
                    <p>Use the contact page for support on scale, materials, matching pieces, or delivery planning.</p>
                </article>
            </section>

            <section class="offer-bottom-cta">
                <div>
                    <span class="offer-kicker">Ready to browse?</span>
                    <h2>See what is currently worth pulling into your space.</h2>
                </div>
                <a href="{{ route('shop') }}" class="offer-primary-link">Explore the shop</a>
            </section>
        </div>
    </section>
</x-layout>
