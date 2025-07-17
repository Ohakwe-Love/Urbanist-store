<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Cart;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\AuthUserRequest;
use App\Models\CartItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;




class AuthController extends Controller
{
    public function register()
    {
        return view('user.register');
    }

    public function store(RegisterUserRequest $request) 
    {
        $validatedData = $request->validated();

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        Auth::login($user);

        return redirect()->route('login')->with("success", "You've been registered successfully, you can now log in");
    }

    public function login()
    {
        return view('user.login');
    }

    public function authenticate(AuthUserRequest $request)
    {
        // Validate the request data
        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Determine if the login field is an email or username

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $login, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))->with('success', 'You are logged in!');
        }

        if (Auth::check()) {
            CartItem::mergeCarts(session()->getId(), Auth::id());
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records!',
        ])->onlyInput('login');
    }

    public function authenticated(Request $request, $user)
    {
        $cartService = app(CartService::class);
        $cartService->mergeGuestCart(Session::getId());
    }

    public function logout(Request $request): RedirectResponse
    {
        // Log out the user
        Auth::logout(); 

        // Invalidate the session
        $request->session()->invalidate(); 

        // Regenerate the CSRF token
        $request->session()->regenerateToken(); 

        return redirect('/')->with('success', 'You
        re successfully logged out!');
    }
}
