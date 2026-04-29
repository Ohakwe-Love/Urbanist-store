<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageNotificationMail;
use App\Models\Admin;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class PageController extends Controller
{
    // About Page
    public function about()
    {
        return view('pages.about');
    }

    // Services Page
    public function services()
    {
        return view('pages.services');
    }

    // Contact Page
    public function contact()
    {
        return view('pages.contact');
    }

    // Help Center Page
    public function helpCenter()
    {
        return view('pages.help-center');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        $contactMessage = ContactMessage::create($validated);

        $recipients = Admin::query()
            ->where('is_active', true)
            ->pluck('email')
            ->push(Setting::valueFor('contact_email', config('mail.from.address')))
            ->filter()
            ->map(fn (string $email) => strtolower(trim($email)))
            ->unique()
            ->values();

        $notificationFailed = false;

        foreach ($recipients as $recipient) {
            try {
                Mail::mailer('failover')->to($recipient)->send(new ContactMessageNotificationMail($contactMessage));
            } catch (Throwable $exception) {
                $notificationFailed = true;

                Log::warning('Contact message email notification failed.', [
                    'contact_message_id' => $contactMessage->id,
                    'recipient' => $recipient,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $message = $notificationFailed
            ? 'Your message has been received. Our team can still review it from the support office, even though email delivery is having a temporary issue.'
            : 'Your message has been received. Our team will get back to you shortly.';

        return back()->with('success', $message);
    }

    // Offer Page
    public function offer()
    {
        return view('pages.offer');
    }

    // News Page
    // public function news()
    // {
    //     return view('pages.news');
    // }

    // Policies Page
    public function policies()
    {
        return view('pages.policies');
    }

    // returns Page
    public function returns()
    {
        return view('pages.returns');
    }

    // cookies Page
    public function cookies()
    {
        return view('pages.cookies');
    }

    // cookies Page
    public function howToOrder()
    {
        return view('pages.how-to-order');
    }
}
