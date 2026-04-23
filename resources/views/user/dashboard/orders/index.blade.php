<x-dashboard-layout :user="$user">
    <x-slot name="title">Order History | Urbanist</x-slot>

    @push('styles')
        <style>
            .orders-panel { background:#fff; border:1px solid #ece7df; border-radius:20px; padding:24px; }
            .orders-stack { display:grid; gap:16px; }
            .order-card { border:1px solid #ece7df; border-radius:18px; padding:20px; background:#faf8f4; }
            .order-top { display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:14px; }
            .order-meta { display:grid; gap:6px; color:#645e56; }
            .status-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
            .status-pill.pending { background:#f7ead1; color:#8a5a00; }
            .status-pill.delivered { background:#dff4e8; color:#1f6a46; }
            .status-pill.shipped { background:#dbeaf8; color:#1d5f9b; }
            .status-pill.cancelled { background:#f8dddd; color:#9d3030; }
            .status-pill.default { background:#ece7df; color:#554f47; }
            .order-summary-row { display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
            .order-items-preview { color:#3c362f; margin-bottom:14px; }
            .order-actions a { display:inline-flex; padding:10px 16px; border-radius:999px; background:#1f5f4a; color:#fff; text-decoration:none; font-weight:600; }
        </style>
    @endpush

    @php
        $pillClass = fn (string $status) => match ($status) {
            'pending' => 'pending',
            'delivered' => 'delivered',
            'shipped' => 'shipped',
            'cancelled' => 'cancelled',
            default => 'default',
        };
    @endphp

    <div class="dashboard-header">
        <div>
            <h1>Order History</h1>
            <p class="welcome-subtext">Track every order, payment state, and delivery milestone from one place.</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

    <section class="orders-panel">
        @if ($orders->isEmpty())
            <div class="order-card">
                <strong>No orders yet.</strong>
                <p style="margin-top:8px; color:#6e6a64;">Once you place an order, it will show up here with payment and shipment progress.</p>
            </div>
        @else
            <div class="orders-stack">
                @foreach ($orders as $order)
                    <article class="order-card">
                        <div class="order-top">
                            <div class="order-meta">
                                <strong>{{ $order->order_number }}</strong>
                                <span>Placed {{ $order->created_at->format('M d, Y') }}</span>
                                <span>Payment: {{ ucfirst($order->payment_status) }} • Fulfillment: {{ ucfirst($order->fulfillment_status) }}</span>
                            </div>
                            <span class="status-pill {{ $pillClass($order->status) }}">{{ $order->status }}</span>
                        </div>

                        <div class="order-summary-row">
                            <span>{{ $order->items->sum('quantity') }} item(s)</span>
                            <strong>${{ number_format((float) $order->total, 2) }}</strong>
                        </div>

                        <div class="order-items-preview">
                            {{ $order->items->pluck('product_title')->implode(', ') }}
                        </div>

                        <div class="order-actions">
                            <a href="{{ route('orders.show', $order) }}">View details</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:20px;">
                {{ $orders->links('vendor.pagination.default') }}
            </div>
        @endif
    </section>
</x-dashboard-layout>
