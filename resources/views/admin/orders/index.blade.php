@extends('admin.layout')

@section('title', 'Orders | Admin')
@section('heading', 'Order Management')
@section('subheading', 'Monitor fulfillment, payment state, and delivery progress.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders">
                <button class="btn btn-secondary" type="submit">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Totals</th>
                        <th>Status</th>
                        <th>Fulfillment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                                <div class="helper-text">{{ $order->created_at->format('M d, Y') }}</div>
                            </td>
                            <td>{{ $order->user?->name ?? $order->shipping_name ?? 'Guest' }}</td>
                            <td>
                                <strong>${{ number_format($order->total, 2) }}</strong>
                                <div class="helper-text">{{ $order->items->sum('quantity') }} items</div>
                            </td>
                            <td><span class="badge badge-neutral">{{ ucfirst($order->status) }}</span></td>
                            <td><span class="badge badge-warning">{{ ucfirst($order->fulfillment_status) }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-link">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">Orders will show here once the checkout pipeline starts creating them.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $orders->links() }}</div>
    </div>
@endsection
