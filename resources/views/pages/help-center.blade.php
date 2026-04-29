<x-layout>
    <x-slot name="title">Help Center | Urbanist</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/help-center.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/help-center.js') }}"></script>
    @endpush

    @php
        $cleanPhone = preg_replace('/[^0-9+]/', '', $storeSettings['phone_number']);
    @endphp

    <section class="help-center-container">
        <div class="help-center-header">
            <div class="page-route"><a href="{{ route('home') }}">Home</a>&ensp;/&ensp; Help Center</div>
            <span class="help-center-kicker">Support desk</span>
            <h1>How can we help?</h1>
            <p>Find quick answers about orders, delivery, returns, and account support. If you still need us, our team is one message away.</p>
        </div>

        <section class="category-grid">
            <article class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 7H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zm0 10H4V9h16zm-6-7H6v2h8zm4 4H6v2h12z"/>
                    </svg>
                </div>
                <h3>Orders & Payments</h3>
                <p>Track your checkout journey, payment confirmations, and what to expect after placing an order.</p>
                <a href="{{ route('dashboard') }}">
                    View account orders
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7-1.4-1.4 4.6-4.6H4v-2h12.2l-4.6-4.6z"/></svg>
                </a>
            </article>

            <article class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 8h-3V4H3v13h2a3 3 0 0 0 6 0h4a3 3 0 0 0 6 0h1v-5zm-5-2v5H5V6zm-7 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm2 0h-1a3 3 0 0 0-6 0h-2a3 3 0 0 0-6 0H5v-2h10V10h4.2L20 11.2z"/>
                    </svg>
                </div>
                <h3>Shipping & Delivery</h3>
                <p>Learn about delivery windows, white-glove setup, shipping updates, and what happens after dispatch.</p>
                <a href="{{ route('how-to-order') }}">
                    Read shipping guide
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7-1.4-1.4 4.6-4.6H4v-2h12.2l-4.6-4.6z"/></svg>
                </a>
            </article>

            <article class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 15h-2v-2h2zm1.1-7.8-.9.9A3 3 0 0 0 12 13h-2v-.5a3.5 3.5 0 0 1 1-2.5l1.2-1.2a1.5 1.5 0 1 0-2.6-1.1H7.6a3.5 3.5 0 1 1 6.5 1.5z"/>
                    </svg>
                </div>
                <h3>Returns & Policies</h3>
                <p>Understand return eligibility, item conditions, policy timelines, and how to start a support request.</p>
                <a href="{{ route('returns') }}">
                    Review returns
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 5l7 7-7 7-1.4-1.4 4.6-4.6H4v-2h12.2l-4.6-4.6z"/></svg>
                </a>
            </article>
        </section>

        <section class="faq-section">
            <h2>Frequently asked questions</h2>

            <div class="faq-item active">
                <button type="button" class="faq-question">
                    <span>How long does delivery usually take?</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg>
                </button>
                <div class="faq-answer">
                    Delivery timing depends on the item type and destination. {{ $storeSettings['shipping_settings'] }} You can also monitor updates from your account once an order has been confirmed.
                </div>
            </div>

            <div class="faq-item">
                <button type="button" class="faq-question">
                    <span>Can I change my address after placing an order?</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg>
                </button>
                <div class="faq-answer">
                    If your order has not been shipped yet, our team can usually help update the delivery address. The fastest option is to contact support immediately with your order number and the corrected address details.
                </div>
            </div>

            <div class="faq-item">
                <button type="button" class="faq-question">
                    <span>Where can I track a paid order?</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg>
                </button>
                <div class="faq-answer">
                    Once you are signed in, your latest orders, payment records, and order statuses are available from the customer dashboard. If a shipment has been created, you will also see fulfillment progress there.
                </div>
            </div>

            <div class="faq-item">
                <button type="button" class="faq-question">
                    <span>What should I do if my payment was debited but the order did not complete?</span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 10l5 5 5-5z"/></svg>
                </button>
                <div class="faq-answer">
                    Please send us your payment reference and the email used during checkout through the contact form. We can review the payment record from the admin office and help reconcile the order if needed.
                </div>
            </div>
        </section>

        <section class="contact-section">
            <h2>Still need help?</h2>
            <p>Our support team can guide you through purchases, payments, delivery updates, or post-order issues. Reach us through the channel that works best for you.</p>
            <div class="contact-methods">
                <article class="contact-method">
                    <div class="contact-method-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z"/>
                        </svg>
                    </div>
                    <h3>Email support</h3>
                    <p>{{ $storeSettings['contact_email'] }}</p>
                    <a href="{{ route('contact') }}">Send a message</a>
                </article>

                <article class="contact-method">
                    <div class="contact-method-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6.6 10.8a15.5 15.5 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.2 11 11 0 0 0 3.4.5 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1 11 11 0 0 0 .5 3.4 1 1 0 0 1-.2 1z"/>
                        </svg>
                    </div>
                    <h3>Call the store</h3>
                    <p>{{ $storeSettings['phone_number'] }}</p>
                    <a href="tel:{{ $cleanPhone }}">Call now</a>
                </article>

                <article class="contact-method">
                    <div class="contact-method-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M19 3H5a2 2 0 0 0-2 2v16l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-2 9H7v-2h10zm0-3H7V7h10z"/>
                        </svg>
                    </div>
                    <h3>Visit contact page</h3>
                    <p>{{ $storeSettings['business_hours'] }}</p>
                    <a href="{{ route('contact') }}">Open contact page</a>
                </article>
            </div>
        </section>
    </section>
</x-layout>
