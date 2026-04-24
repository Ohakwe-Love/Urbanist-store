<x-form-layout>
    <x-slot name="title">Verify Email | Urbanist Store</x-slot>

    <div class="form-container auth-card auth-card-narrow">
        <a href="{{ route('home') }}" class="auth-logo">
            <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
        </a>

        <div class="auth-progress" aria-label="Registration progress">
            <div class="auth-progress-item is-active">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Enter email</span>
            </div>
            <div class="auth-progress-item">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Verify email</span>
            </div>
            <div class="auth-progress-item">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Details</span>
            </div>
        </div>
        <h2>Verify your email</h2>
        <p class="auth-subtext">Enter your email address first. We’ll send you a six-digit code before you can continue.</p>

        <form action="{{ route('register.email') }}" method="POST">
            @csrf

            <div class="input-row input-row-single">
                <div class="input-group span2">
                    <label for="email">email</label>
                    <input type="email" name="email" id="email" placeholder="sampeter@google.com" value="{{ old('email') }}">
                </div>
            </div>

            <button class="submit-btn">Send verification code</button>
        </form>

        <p class="login-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</x-form-layout>
