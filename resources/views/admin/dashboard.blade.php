@extends('admin.layout')

@section('title', 'Admin Dashboard | Urbanist')
@section('heading', 'Dashboard')
@section('subheading', 'A quick read on store activity, inventory pressure, and customer growth.')

@section('content')
    <section class="admin-grid stats">
        <div class="stat-card"><span>Total products</span><strong>{{ $stats['products'] }}</strong></div>
        <div class="stat-card"><span>Featured products</span><strong>{{ $stats['featured_products'] }}</strong></div>
        <div class="stat-card"><span>Low stock products</span><strong>{{ $stats['low_stock_products'] }}</strong></div>
        <div class="stat-card"><span>Total users</span><strong>{{ $stats['users'] }}</strong></div>
        <div class="stat-card"><span>Active users</span><strong>{{ $stats['active_users'] }}</strong></div>
        <div class="stat-card"><span>Total orders</span><strong>{{ $stats['orders'] }}</strong></div>
        <div class="stat-card"><span>Pending orders</span><strong>{{ $stats['pending_orders'] }}</strong></div>
        <div class="stat-card"><span>Completed orders</span><strong>{{ $stats['completed_orders'] }}</strong></div>
        <div class="stat-card"><span>Total revenue</span><strong>${{ number_format($stats['revenue'], 2) }}</strong></div>
        <div class="stat-card"><span>Categories</span><strong>{{ $stats['categories'] }}</strong></div>
        <div class="stat-card"><span>Active coupons</span><strong>{{ $stats['active_coupons'] }}</strong></div>
        <div class="stat-card"><span>Pending reviews</span><strong>{{ $stats['pending_reviews'] }}</strong></div>
    </section>

    <section class="two-col">
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Low Stock Watchlist</h2>
                    <a href="{{ route('admin.products.index') }}" class="btn-link">Manage products</a>
                </div>

                @if ($lowStockProducts->isEmpty())
                    <div class="empty-state">No products are close to running out right now.</div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->title }}</td>
                                        <td>{{ $product->category }}</td>
                                        <td>
                                            <span class="badge {{ $product->stock_quantity <= 5 ? 'badge-warning' : 'badge-success' }}">
                                                {{ $product->stock_quantity }} left
                                            </span>
                                        </td>
                                        <td><a class="btn-link" href="{{ route('admin.products.edit', $product) }}">Edit</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Recent Orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="btn-link">View all orders</a>
                </div>

                @if ($recentOrders->isEmpty())
                    <div class="empty-state">No orders have been placed yet.</div>
                @else
                    <div class="metric-list">
                        @foreach ($recentOrders as $order)
                            <div>
                                <div>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="helper-text">{{ $order->user?->name ?? 'Customer account unavailable' }}</div>
                                </div>
                                <div>
                                    <span class="badge badge-neutral">{{ ucfirst($order->status) }}</span>
                                    <div class="helper-text">${{ number_format($order->total, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Recent Users</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn-link">Manage users</a>
                </div>

                @if ($recentUsers->isEmpty())
                    <div class="empty-state">No user registrations yet.</div>
                @else
                    <div class="metric-list">
                        @foreach ($recentUsers as $user)
                            <div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <div class="helper-text">{{ $user->email }}</div>
                                </div>
                                <div>
                                    <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Store Health</h2>
                </div>
                <div class="detail-list">
                    <div><span>Inventory pressure</span><strong>{{ $stats['low_stock_products'] }} products low</strong></div>
                    <div><span>Order flow</span><strong>{{ $stats['pending_orders'] }} pending orders</strong></div>
                    <div><span>Review queue</span><strong>{{ $stats['pending_reviews'] }} pending approvals</strong></div>
                    <div><span>Campaign readiness</span><strong>{{ $stats['active_coupons'] }} active coupons</strong></div>
                </div>
            </div>
        </div>
    </section>
@endsection
