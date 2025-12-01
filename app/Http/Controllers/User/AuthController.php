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
use Illuminate\Support\Facades\Session;
use App\Services\CartService;

class AuthController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function register()
    {
        return view('user.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
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
        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Capturing the OLD session ID BEFORE login
        $oldSessionId = Session::getId();

        if (Auth::attempt([$fieldType => $login, 'password' => $password], $remember)) {
            
            // Merge the cart using the OLD session ID
            $this->mergeCartWithOldSession($oldSessionId);
            
            // THEN regenerate session
            $request->session()->regenerate();
            
            return redirect()->intended(route('home'))->with('success', 'You are logged in!');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records!',
        ])->onlyInput('login');
    }

    /**
     * Merge guest cart using the old session ID
     */
    protected function mergeCartWithOldSession(string $oldSessionId): void
    {
        if (!Auth::check()) {
            return;
        }

        // Find the guest cart using the OLD session ID
        $sessionCart = Cart::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->first();

        if (!$sessionCart || $sessionCart->items->isEmpty()) {
            return;
        }

        // Get or create the user's cart
        $userCart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        // Don't merge if it's somehow the same cart
        if ($sessionCart->id === $userCart->id) {
            return;
        }

        // Merge each item
        foreach ($sessionCart->items as $sessionItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $sessionItem->product_id)
                ->first();

            if ($existingItem) {
                // Combine quantities
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $sessionItem->quantity
                ]);
            } else {
                // Create new item in user cart
                CartItem::create([
                    'cart_id' => $userCart->id,
                    'product_id' => $sessionItem->product_id,
                    'quantity' => $sessionItem->quantity,
                    'price' => $sessionItem->price
                ]);
            }
        }

        // Clean up the old session cart
        $sessionCart->items()->delete();
        $sessionCart->delete();
    }

    // Remove this method - it's not being used
    // public function authenticated(Request $request, $user) { ... }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You are successfully logged out!');
    }
}