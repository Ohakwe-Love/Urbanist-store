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
use App\Models\RegistrationEmailVerification;
use App\Notifications\VerifyRegistrationEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use App\Services\CartService;

class AuthController extends Controller
{
    protected const REGISTRATION_PENDING_EMAIL = 'registration.pending_email';
    protected const REGISTRATION_VERIFIED_EMAIL = 'registration.verified_email';

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function register()
    {
        return view('user.register-email');
    }

    public function showRegistrationCodeForm(Request $request)
    {
        $pendingEmail = $request->session()->get(self::REGISTRATION_PENDING_EMAIL);

        if (!$pendingEmail) {
            return redirect()->route('register');
        }

        return view('user.register-verify', [
            'pendingEmail' => $pendingEmail,
        ]);
    }

    public function showRegistrationDetailsForm(Request $request)
    {
        $verifiedEmail = $this->verifiedRegistrationEmail($request);

        if (!$verifiedEmail) {
            return redirect()->route('register');
        }

        return view('user.register', [
            'verifiedEmail' => $verifiedEmail,
        ]);
    }

    public function sendRegistrationVerificationEmail(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
        ]);

        $verificationCode = (string) random_int(100000, 999999);

        RegistrationEmailVerification::updateOrCreate(
            ['email' => $validatedData['email']],
            [
                'token' => Hash::make($verificationCode),
                'expires_at' => now()->addHour(),
                'verified_at' => null,
            ]
        );

        Notification::route('mail', $validatedData['email'])
            ->notify(new VerifyRegistrationEmail($validatedData['email'], $verificationCode));

        $request->session()->forget(self::REGISTRATION_VERIFIED_EMAIL);
        $request->session()->put(self::REGISTRATION_PENDING_EMAIL, $validatedData['email']);

        return redirect()->route('register.verify')->with(
            'success',
            'We sent a verification code to '.$validatedData['email'].'.'
        );
    }

    public function verifyRegistrationCode(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:100'],
            'code' => ['required', 'digits:6'],
        ]);

        if (User::where('email', $validatedData['email'])->exists()) {
            return redirect()->route('login')->with('success', 'That email is already registered. Please log in instead.');
        }

        $verification = RegistrationEmailVerification::where('email', $validatedData['email'])->first();

        if (!$verification || $verification->expires_at->isPast() || !Hash::check($validatedData['code'], $verification->token)) {
            return back()
                ->withErrors(['code' => 'That verification code is invalid or has expired. Please request a new one.'])
                ->withInput();
        }

        $verification->forceFill([
            'verified_at' => now(),
        ])->save();

        $request->session()->forget(self::REGISTRATION_PENDING_EMAIL);
        $request->session()->put(self::REGISTRATION_VERIFIED_EMAIL, $validatedData['email']);

        return redirect()->route('register.details')->with('success', 'Email verified. Complete your account details.');
    }

    public function resetRegistrationVerification(Request $request): RedirectResponse
    {
        $request->session()->forget([
            self::REGISTRATION_PENDING_EMAIL,
            self::REGISTRATION_VERIFIED_EMAIL,
        ]);

        return redirect()->route('register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $verifiedEmail = $this->verifiedRegistrationEmail($request);

        if (!$verifiedEmail) {
            return redirect()
                ->route('register.verify')
                ->with('error', 'Verify your email first before completing registration.');
        }

        $validatedData = $request->validated();
        $validatedEmail = strtolower($validatedData['email']);

        if ($validatedEmail !== strtolower($verifiedEmail)) {
            return back()
                ->withErrors(['email' => 'Use the same verified email address to finish registration.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $verification = RegistrationEmailVerification::verified()
            ->where('email', $validatedData['email'])
            ->first();

        if (!$verification) {
            return redirect()
                ->route('register.verify')
                ->with('error', 'Your verification session could not be confirmed. Please verify your email again.');
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['email_verified_at'] = now();
        $validatedData['role'] = User::ROLE_CUSTOMER;
        $validatedData['is_active'] = true;
        unset($validatedData['agreement']);

        User::create($validatedData);
        $verification->delete();
        $request->session()->forget([
            self::REGISTRATION_PENDING_EMAIL,
            self::REGISTRATION_VERIFIED_EMAIL,
        ]);

        return redirect()->route('login')->with('success', "You've been registered successfully. You can now log in.");
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
            if (is_null(Auth::user()?->email_verified_at)) {
                Auth::logout();

                return back()->withErrors([
                    'login' => 'Please verify your email address before logging in.',
                ])->onlyInput('login');
            }
            
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

    protected function verifiedRegistrationEmail(Request $request): ?string
    {
        $email = $request->session()->get(self::REGISTRATION_VERIFIED_EMAIL);

        if (!$email) {
            return null;
        }

        $verification = RegistrationEmailVerification::verified()
            ->where('email', $email)
            ->first();

        if (!$verification) {
            $request->session()->forget(self::REGISTRATION_VERIFIED_EMAIL);

            return null;
        }

        return $email;
    }
}
