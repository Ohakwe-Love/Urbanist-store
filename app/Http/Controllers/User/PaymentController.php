<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $payments = Payment::query()
            ->with('order')
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(10);

        return view('user.dashboard.payments.index', compact('payments', 'user'));
    }
}
