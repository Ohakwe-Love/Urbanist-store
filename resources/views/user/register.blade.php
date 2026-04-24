<x-form-layout>
    <x-slot name="title">Complete Registration | Urbanist Store</x-slot>

    <div class="form-container auth-card auth-card-narrow">
        <a href="{{ route('home') }}" class="auth-logo">
            <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
        </a>

        <div class="auth-progress" aria-label="Registration progress">
            <div class="auth-progress-item is-complete">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Enter email</span>
            </div>
            <div class="auth-progress-item is-complete">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Verify email</span>
            </div>
            <div class="auth-progress-item is-active">
                <span class="auth-progress-check"><i class="fa-solid fa-check"></i></span>
                <span>Details</span>
            </div>
        </div>
        <h2>Complete your account</h2>
        <p class="auth-subtext">Your email has been verified. Add your details below to finish registration.</p>

        <div class="form-status-card success compact">
            <strong>{{ $verifiedEmail }}</strong>
            <p>This verified email will be used for your Urbanist account.</p>
        </div>

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="input-row">
                <div class="input-group">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" placeholder="Sam Peters" value="{{ old('name') }}">
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="SammyPete" value="{{ old('username') }}">
                </div>
                <div class="input-group span2">
                    <label for="email">email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $verifiedEmail) }}" readonly>
                </div>
                <div class="input-group">
                    <label for="password">password</label>
                    <div class="password">
                        <input type="password" id="password" name="password" class="password-input" placeholder="********">
                        <span class="passwordToggle"><i class="fa-regular fa-eye-slash"></i></span>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password_confirmation">confirm password</label>
                    <div class="password">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="password-input" placeholder="********">
                        <span class="passwordToggle"><i class="fa-regular fa-eye-slash"></i></span>
                    </div>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="agreement" id="agreement" class="checkbox-input" value="1" {{ old('agreement') ? 'checked' : '' }}>
                <label for="agreement" class="checkbox-label">I agree with <a href="{{ route('policies') }}">Privacy Policy</a>, <a href="{{ route('policies') }}">Terms of Service</a></label>
            </div>

            <button class="submit-btn">Create account</button>
        </form>

        <form action="{{ route('register.change-email') }}" method="POST" class="auth-secondary-form">
            @csrf
            <button type="submit" class="secondary-btn">Start again with another email</button>
        </form>

        <p class="login-link">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</x-form-layout>
