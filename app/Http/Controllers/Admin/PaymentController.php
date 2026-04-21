<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.payments.index', [
            'payments' => Payment::with('order')->latest()->paginate(12),
        ]);
    }

    public function edit(Payment $payment): View
    {
        $payment->load('order');

        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'method' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment->update([
            'payment_reference' => $validated['payment_reference'] ?? null,
            'method' => $validated['method'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'paid_at' => $validated['status'] === 'successful' ? ($payment->paid_at ?? Carbon::now()) : null,
            'disputed_at' => $validated['status'] === 'disputed' ? Carbon::now() : null,
        ]);

        if ($payment->order) {
            $payment->order->update([
                'payment_status' => $validated['status'] === 'successful' ? 'paid' : $validated['status'],
            ]);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully.');
    }
}
