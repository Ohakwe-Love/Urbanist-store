<x-form-layout>
    <x-slot name="title">Create New Password | Urbanist Store</x-slot>

    <div class="form-container">
        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
            </a>
            <h2>Create a new password</h2>
            <p style="text-align:center; margin-bottom:20px; color:#666;">Choose a strong new password for your Urbanist account.</p>

            <div class="input-row">
                <div class="input-group span2">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
                </div>
                <div class="input-group span2">
                    <label for="password">New password</label>
                    <div class="password">
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="input-group span2">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="password">
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
            </div>

            <button class="submit-btn">Update password</button>

            <p class="login-link">Need to sign in instead? <a href="{{ route('login') }}">Back to login</a></p>
        </form>
    </div>
</x-form-layout>
