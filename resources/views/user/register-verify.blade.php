<x-form-layout>
    <x-slot name="title">Enter Verification Code | Urbanist Store</x-slot>

    <div class="form-container auth-card auth-card-narrow">
        <a href="{{ route('home') }}" class="auth-logo">
            <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
        </a>

        <div class="auth-progress" aria-label="Registration progress">
            <div class="auth-progress-item is-complete">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Enter email</span>
            </div>
            <div class="auth-progress-item is-active">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Verify email</span>
            </div>
            <div class="auth-progress-item">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Details</span>
            </div>
        </div>
        <h2>Enter verification code</h2>
        <p class="auth-subtext">We sent a six-digit code to <strong>{{ $pendingEmail }}</strong>. Enter it below to continue.</p>

        <form action="{{ route('register.verify-code') }}" method="POST" class="verification-code-form">
            @csrf
            <input type="hidden" name="email" value="{{ $pendingEmail }}">

            <label for="verification-code" class="verification-code-label">Verification code</label>
            <input
                type="text"
                id="verification-code"
                name="code"
                class="verification-code-input"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                placeholder="000000"
                value="{{ old('code') }}"
            >

            <button type="submit" class="submit-btn">Verify code</button>
        </form>

        <form action="{{ route('register.email') }}" method="POST" class="auth-secondary-form">
            @csrf
            <input type="hidden" name="email" value="{{ $pendingEmail }}">
            <button type="submit" class="secondary-btn">Resend verification code</button>
        </form>

        <form action="{{ route('register.change-email') }}" method="POST" class="auth-secondary-form">
            @csrf
            <button type="submit" class="secondary-btn">Use another email</button>
        </form>

        <p class="login-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</x-form-layout>
