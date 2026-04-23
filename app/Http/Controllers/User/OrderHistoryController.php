<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('user.dashboard.orders.index', [
            'user' => $user,
            'orders' => $user->orders()->with(['items', 'payment', 'shipment'])->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $order->load(['items.product', 'payment', 'shipment']);

        return view('user.dashboard.orders.show', [
            'user' => auth()->user(),
            'order' => $order,
        ]);
    }
}
