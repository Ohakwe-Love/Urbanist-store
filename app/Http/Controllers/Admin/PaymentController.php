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
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        return view('admin.payments.index', [
            'payments' => Payment::query()
                ->with('order')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('payment_reference', 'like', "%{$search}%")
                            ->orWhere('method', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('order', function ($orderQuery) use ($search) {
                                $orderQuery->where('order_number', 'like', "%{$search}%")
                                    ->orWhere('shipping_name', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
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
