<x-form-layout>
    <x-slot name="title">Login  | Urbanist Store</x-slot>

    <div class="form-container">
        <form action="{{route('login.authenticate')}}" method="POST">
            @csrf

            <a href="{{route('home')}}" class="auth-logo">
                <img src="{{asset('assets/images/logo/logo-dark.webp')}}" alt="">    
            </a>  
            <h2>Login to your Account</h2> 

            <div class="social-login">
                <a href="" class="social-btn facebook-btn">
                    <img src="{{asset('assets/images/socials/facebook.svg')}}" alt="">
                </a>
                <a href="" class="social-btn google-btn">
                    <img src="{{asset('assets/images/socials/google.svg')}}" alt="">
                </a>
                <a href="" class="social-btn linkedin-btn">
                    <img src="{{asset('assets/images/socials/linkedin.svg')}}" alt="">
                </a>
            </div>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="input-row">
                <div class="input-group span2">
                    <label for="login">Enter Email or Username</label>
                    <input type="text" name="login" placeholder="Email or Username" value="{{old('login')}}">
                </div>
                <div class="input-group span2">
                    <label for="password">password</label>
                    <div class="password">
                        <input type="password" name="password" id="password" required>
                        {{-- <span class="hide-password"><i class="fa-regular fa-eye-slash"></i></span> --}}
                    </div>
                </div>
            </div>

            {{-- <div class="recaptcha"></div>  --}}

            <div class="others">
                {{-- <div class="checkbox-group">
                    <input type="checkbox" name="" id="" class="checkbox-input">
                    <label for="" class="checkbox-label">Remember me</label>
                </div> --}}

                <a href="" class="forgot-password">Forgot password?</a>
            </div>

            <button class="submit-btn">Login</button>

            <p class="login-link">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </form>
    </div> 
</x-form-layout>
{{-- <i class="fa-regular fa-eye"></i> --}}