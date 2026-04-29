<x-layout>
    <x-slot name="title">Services | Urbanist</x-slot>

    <section class="section about-company">
        <div class="container" style="padding: 80px 0 30px;">
            <div class="page-route"><a href="{{ route('home') }}">Home</a>&ensp;/&ensp; Services</div>
            <div style="max-width: 760px; margin-top: 20px;">
                <h1 style="font-size: clamp(2.4rem, 4vw, 4rem); margin-bottom: 16px; color: var(--secondary-color);">Design-led services for modern homes.</h1>
                <p style="font-size: 1.05rem; line-height: 1.8; color: #5f6f73;">
                    Urbanist supports you from product selection through delivery, styling, and setup. Whether you are furnishing one room or building out an entire home, our team helps you create spaces that feel calm, practical, and beautifully lived in.
                </p>
            </div>
        </div>
    </section>

    <x-services-grid />
</x-layout>
