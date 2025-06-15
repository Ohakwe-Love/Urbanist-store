<x-form-layout>
    <x-slot name="title">Register  | Urbanist Store</x-slot>

    <div class="form-container">
        <form action="{{route('register.store')}}" method="POST">
            @csrf

            <a href="{{route('home')}}" class="auth-logo">
                <img src="{{asset('assets/images/logo/logo-dark.webp')}}" alt="">    
            </a>  
            <h2>Create an Account</h2> 

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
                <div class="input-group">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" placeholder="Sam Peters" value="{{old('name')}}">
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="SammyPete" value="{{old('username')}}">
                </div>
                <div class="input-group span2">
                    <label for="email">email</label>
                    <input type="email" name="email" id="email" placeholder="sampeter@google.com" value="{{old('email')}}">
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

            {{-- <div class="recaptcha"></div> --}}

            <div class="checkbox-group">
                <input type="checkbox" name="agreement" id="agreement" class="checkbox-input">
                <label for="agreement" class="checkbox-label">I agree with <a href="{{route('policies')}}">Privacy Policy</a>, <a href="{{route('policies')}}">Terms of Service</a></label>
            </div>

            <button class="submit-btn">Register</button>

            <p class="login-link">Already have an account? <a href="{{route('login')}}">Login</a></p>
        </form>
    </div> 
</x-form-layout>
{{-- <i class="fa-regular fa-eye"></i> --}}