<x-dashboard-layout :user="$user">
    <x-slot name="title">{{ $order->order_number }} | Urbanist</x-slot>

    @push('styles')
        <style>
            .order-detail-grid { display:grid; gap:24px; grid-template-columns:1.3fr .9fr; }
            .order-detail-panel { background:#fff; border:1px solid #ece7df; border-radius:20px; padding:24px; }
            .order-detail-panel h2 { margin-bottom:14px; }
            .order-line-items { display:grid; gap:14px; }
            .order-line-item { display:flex; justify-content:space-between; gap:16px; padding:16px 0; border-top:1px solid #ece7df; }
            .order-line-item:first-child { border-top:none; padding-top:0; }
            .muted-copy { color:#6b655d; }
            .detail-list { display:grid; gap:10px; color:#3d372f; }
            .back-link { display:inline-flex; margin-bottom:16px; color:#1f5f4a; font-weight:600; text-decoration:none; }
            @media (max-width: 900px) {
                .order-detail-grid { grid-template-columns:1fr; }
            }
        </style>
    @endpush

    <div class="dashboard-header">
        <div>
            <h1>{{ $order->order_number }}</h1>
            <p class="welcome-subtext">Placed {{ $order->created_at->format('F d, Y') }} • Status: {{ ucfirst($order->status) }}</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

    <a class="back-link" href="{{ route('orders.index') }}">← Back to order history</a>

    <div class="order-detail-grid">
        <section class="order-detail-panel">
            <h2>Items ordered</h2>

            <div class="order-line-items">
                @foreach ($order->items as $item)
                    <div class="order-line-item">
                        <div>
                            <strong>{{ $item->product_title }}</strong>
                            <div class="muted-copy">Qty {{ $item->quantity }}</div>
                            @if ($item->attributes)
                                <div class="muted-copy">
                                    {{ collect($item->attributes)->filter()->map(fn ($value, $key) => ucfirst($key) . ': ' . $value)->implode(' • ') }}
                                </div>
                            @endif
                        </div>
                        <strong>{{ $order->currency_symbol }}{{ number_format((float) $item->total_price, 2) }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="order-detail-panel">
            <h2>Order summary</h2>
            <div class="detail-list">
                <span>Subtotal: {{ $order->currency_symbol }}{{ number_format((float) $order->subtotal, 2) }}</span>
                <span>Discount: {{ $order->currency_symbol }}{{ number_format((float) $order->discount_total, 2) }}</span>
                <span>Shipping: {{ $order->currency_symbol }}{{ number_format((float) $order->shipping_fee, 2) }}</span>
                <strong>Total: {{ $order->currency_symbol }}{{ number_format((float) $order->total, 2) }}</strong>
            </div>

            <h2 style="margin-top:24px;">Payment</h2>
            <div class="detail-list">
                <span>Method: {{ ucfirst($order->payment?->method ?? 'Not set') }}</span>
                <span>Status: {{ ucfirst($order->payment_status) }}</span>
                <span>Reference: {{ $order->payment?->payment_reference ?? 'Pending' }}</span>
                <span>Paid at: {{ $order->payment?->paid_at?->format('M d, Y h:i A') ?? 'Not paid yet' }}</span>
            </div>

            <h2 style="margin-top:24px;">Delivery</h2>
            <div class="detail-list">
                <span>Recipient: {{ $order->shipping_name ?: $user->name }}</span>
                <span>Address: {{ collect([$order->shipping_address, $order->shipping_city, $order->shipping_state, $order->shipping_postal_code, $order->shipping_country])->filter()->implode(', ') ?: 'No shipping address captured.' }}</span>
                <span>Carrier: {{ $order->shipment?->carrier ?? 'Pending assignment' }}</span>
                <span>Tracking: {{ $order->shipment?->tracking_number ?? 'Pending' }}</span>
                <span>Shipment status: {{ ucfirst($order->shipment?->status ?? 'pending') }}</span>
            </div>

            @if ($order->notes)
                <h2 style="margin-top:24px;">Notes</h2>
                <p class="muted-copy">{{ $order->notes }}</p>
            @endif
        </aside>
    </div>
</x-dashboard-layout>
