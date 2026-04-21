<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (!$admin || !$admin->is_active) {
            return back()->withErrors([
                'email' => 'These admin credentials are not valid.',
            ])->onlyInput('email');
        }

        if (Auth::guard('admin')->attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, admin.');
        }

        return back()->withErrors([
            'email' => 'These admin credentials are not valid.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Admin session ended successfully.');
    }
}
