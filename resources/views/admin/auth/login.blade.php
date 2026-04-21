<x-form-layout>
    <x-slot name="title">Admin Login | Urbanist</x-slot>

    <div class="form-container">
        <form action="{{ route('admin.authenticate') }}" method="POST">
            @csrf

            <a href="{{ route('home') }}" class="auth-logo">
                <img src="{{ asset('assets/images/logo/logo-dark.webp') }}" alt="">
            </a>
            <h2>Admin Access</h2>
            <p style="text-align:center; margin-bottom:20px; color:#666;">Use your dedicated admin account to manage the store.</p>

            <div class="input-row">
                <div class="input-group span2">
                    <label for="email">Admin email</label>
                    <input type="email" id="email" name="email" placeholder="admin@urbanist.com" value="{{ old('email') }}" required>
                </div>
                <div class="input-group span2">
                    <label for="password">Password</label>
                    <div class="password">
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="remember" id="remember" class="checkbox-input" value="1">
                <label for="remember" class="checkbox-label">Keep me signed in on this device</label>
            </div>

            <button class="submit-btn">Enter admin panel</button>

            <p class="login-link">Customer account? <a href="{{ route('login') }}">Go to customer login</a></p>
        </form>
    </div>
</x-form-layout>
