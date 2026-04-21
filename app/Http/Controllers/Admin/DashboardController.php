<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'low_stock_products' => Product::where('stock_quantity', '<=', 5)->count(),
            'users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'revenue' => Payment::where('status', 'successful')->sum('amount'),
            'categories' => Category::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'lowStockProducts' => Product::orderBy('stock_quantity')->take(6)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentOrders' => Order::latest()->with('user')->take(5)->get(),
        ]);
    }
}
