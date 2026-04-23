<x-form-layout>
    <x-slot name="title">Forgot Password | Urbanist Store</x-slot>

    <div class="form-container">
        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
            </a>
            <h2>Reset your password</h2>
            <p style="text-align:center; margin-bottom:20px; color:#666;">Enter the email on your account and we will send a reset link.</p>

            <div class="input-row">
                <div class="input-group span2">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" placeholder="you@urbanist-store.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <button class="submit-btn">Send reset link</button>

            <p class="login-link">Remembered your password? <a href="{{ route('login') }}">Back to login</a></p>
        </form>
    </div>
</x-form-layout>
