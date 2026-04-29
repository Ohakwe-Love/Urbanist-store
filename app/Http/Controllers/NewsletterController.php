<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew([
            'email' => strtolower($validated['email']),
        ]);

        $subscriber->fill([
            'is_active' => true,
            'subscribed_at' => $subscriber->subscribed_at ?? now(),
        ]);
        $subscriber->save();

        return back()->with('success', 'You have been added to the Urbanist newsletter.');
    }
}
