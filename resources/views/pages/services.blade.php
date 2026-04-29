<x-layout>
    <x-slot name="title">Services | Urbanist</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/services.css') }}">
    @endpush

    <section class="services-page">
        <div class="services-hero">
            <div class="container">
                <div class="page-route"><a href="{{ route('home') }}">Home</a>&ensp;/&ensp; Services</div>
                <div class="services-hero-grid">
                    <div class="services-hero-copy">
                        <span class="services-kicker">Urbanist service studio</span>
                        <h1>Design-led services for modern homes.</h1>
                        <p>Urbanist supports you from product selection through delivery, styling, and setup. Whether you are furnishing one room or building out an entire home, our team helps you create spaces that feel calm, practical, and beautifully lived in.</p>
                    </div>
                    <div class="services-hero-note">
                        <strong>What we help with</strong>
                        <p>Product pairing, material guidance, room finishing, shipping coordination, and post-purchase support that feels human from start to finish.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <section class="services-story-grid">
                <article class="services-story-card">
                    <span class="services-kicker">Consultation</span>
                    <h2>Make better furniture decisions before you commit.</h2>
                    <p>We help customers think through proportions, finishes, and pairings so purchases feel intentional instead of rushed.</p>
                </article>

                <article class="services-story-card">
                    <span class="services-kicker">Delivery support</span>
                    <h2>Large pieces need planning, not just shipping.</h2>
                    <p>From access questions to handling expectations, we guide the details that make furniture delivery smoother and less stressful.</p>
                </article>
            </section>

            <x-services-grid />

            <section class="services-process">
                <div class="services-process-head">
                    <span class="services-kicker">How it works</span>
                    <h2>A calmer path from inspiration to installation.</h2>
                </div>

                <div class="services-process-grid">
                    <article>
                        <strong>01</strong>
                        <h3>Share the room need</h3>
                        <p>Tell us what you are furnishing, where the pressure points are, and what kind of finish you want the space to carry.</p>
                    </article>
                    <article>
                        <strong>02</strong>
                        <h3>Refine the selection</h3>
                        <p>We help narrow product choices based on scale, use case, materials, and how pieces should work together.</p>
                    </article>
                    <article>
                        <strong>03</strong>
                        <h3>Plan fulfillment</h3>
                        <p>Once you are ready, we guide the delivery, timing, and order details so the final step feels controlled.</p>
                    </article>
                </div>
            </section>
        </div>
    </section>
</x-layout>
