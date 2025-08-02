<x-layout>
    <x-slot name="title">Contact Us  | Urbanist Store</x-slot>
    {{-- <x-slot name="description">Get in touch with Urbanist for inquiries, support, or feedback. We are here to assist you!</x-slot>
    <x-slot name="keywords">Contact, Support, Inquiries, Urbanist</x-slot>
    <x-slot name="canonical">https://urbanist.com/contact</x-slot>
    <x-slot name="ogTitle">Contact Us  | Urbanist</x-slot>
    <x-slot name="ogDescription">Get in touch with Urbanist for inquiries, support, or feedback. We are here to assist you!</x-slot> --}}

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
    @endpush

    {{-- <div class="page-route"><a href="{{route('home')}}">Home</a>&ensp;/&ensp; Contact</div> --}}
    <section class="contactWrapper">
        <form action="" class="contactForm">
            <h2>Get in Touch</h2>
            <p>Please enter the details of your requesst. A member of our support staff will respond as soon as possible.</p>

            <div class="input-group">
                <div>
                    <input type="text" name="name" id="name" placeholder="your name">
                </div>
                <div>
                    <input type="email" name="email" id="email" placeholder="your email">
                </div>
            </div>

            <div class="input-group">
                <input type="number" name="phoneNumber" id="phoneNumber" placeholder="Phone number">
            </div>

            <div class="input-group">
                <textarea name="message" id="message" placeholder="your message"></textarea>
            </div>

            <button>submit</button>
        </form>

        <div class="contactAddress">
            <h2>Contact Info</h2>
            <p>Feel free to reach out to us. Urbanist cares.</p>
            <ul>
                <li><span>Address:</span>123 Suspendis matti, Visaosang Building VST District, NY Accums, North American</li>
                <li><span>Email:</span> support@domain.com</li>
                <li><span>Call Us:</span> (012)-345-67890</li>
                <li><span>Opening time:</span> Our store has re-opened for shopping, exchanges every day <span>11am to 7pm</span></li>
            </ul>
        </div>
    </section>
</x-layout>