<x-layout>
    <x-slot name="title">Contact Us  | Urbanist Store</x-slot>
    @php
        $defaultBlocks = \App\Models\ContentBlock::defaults();
        $contactBlock = ($contentBlocks['contact_details']['is_active'] ?? true) ? ($contentBlocks['contact_details'] ?? $defaultBlocks['contact_details']) : $defaultBlocks['contact_details'];
    @endphp
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
        <form action="{{ route('contact.submit') }}" method="POST" class="contactForm">
            @csrf
            <h2>{{ $contactBlock['title'] }}</h2>
            <p>{{ $contactBlock['content'] }}</p>

            <div class="input-group">
                <div>
                    <input type="text" name="name" id="name" placeholder="your name" value="{{ old('name') }}">
                </div>
                <div>
                    <input type="email" name="email" id="email" placeholder="your email" value="{{ old('email') }}">
                </div>
            </div>

            <div class="input-group">
                <input type="text" name="phone" id="phone" placeholder="Phone number" value="{{ old('phone') }}">
            </div>

            <div class="input-group">
                <textarea name="message" id="message" placeholder="your message">{{ old('message') }}</textarea>
            </div>

            <button type="submit">submit</button>
        </form>

        <div class="contactAddress">
            <h2>{{ $contactBlock['meta']['sidebar_heading'] ?? 'Contact Info' }}</h2>
            <p>{{ $contactBlock['meta']['sidebar_content'] ?? 'Feel free to reach out to us. Urbanist cares.' }}</p>
            <ul>
                <li><span>Address:</span>{{ $storeSettings['address'] }}</li>
                <li><span>Email:</span> {{ $storeSettings['contact_email'] }}</li>
                <li><span>Call Us:</span> {{ $storeSettings['phone_number'] }}</li>
                <li><span>Opening time:</span> {{ $storeSettings['business_hours'] }}</li>
            </ul>
        </div>
    </section>
</x-layout>
