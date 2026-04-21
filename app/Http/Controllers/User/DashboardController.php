<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $recentOrders = $user->orders()->latest()->take(3)->get();
        $wishlistCount = $user->visibleWishlistProducts()->count();
        $orderCount = $user->orders()->count();
        $paymentCount = Payment::query()
            ->where('status', 'successful')
            ->whereHas('order', fn ($query) => $query->where('user_id', $user->id))
            ->count();

        return view('user.dashboard.index', compact(
            'user',
            'recentOrders',
            'wishlistCount',
            'orderCount',
            'paymentCount'
        ));
    }
}
