<x-layout>
    <x-slot name="title">{{ $order->order_number }} | Order Complete</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    @endpush

    <div class="checkout-shell checkout-shell-complete">
        <section class="checkout-complete-card">
            <div class="checkout-complete-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1>Order Placed Successfully!</h1>
            <p class="checkout-complete-copy">Thank you for your order. Your payment has been verified and your order is now in our processing queue.</p>

            <div class="checkout-complete-panel">
                <div class="checkout-complete-header">
                    <div>
                        <small>Order</small>
                        <strong>{{ $order->order_number }}</strong>
                    </div>
                    <div>
                        <small>Date</small>
                        <strong>{{ $order->created_at->format('M d, Y h:i A') }}</strong>
                    </div>
                </div>

                <div class="checkout-status-banner">
                    <span class="checkout-status-pill">Payment successful</span>
                    <p>Your payment has been verified. We’ll send updates as your order moves through fulfillment.</p>
                </div>

                <div class="checkout-detail-grid">
                    <div>
                        <small>Payment method</small>
                        <strong>{{ ucfirst($order->payment?->method ?? 'Paystack') }}</strong>
                    </div>
                    <div>
                        <small>Payment status</small>
                        <strong>{{ ucfirst($order->payment_status) }}</strong>
                    </div>
                    <div>
                        <small>Order status</small>
                        <strong>{{ ucfirst($order->status) }}</strong>
                    </div>
                    <div>
                        <small>Reference</small>
                        <strong>{{ $order->payment?->payment_reference ?? 'Pending' }}</strong>
                    </div>
                </div>

                <div class="checkout-complete-items">
                    <h2>Order Items</h2>
                    @foreach ($order->items as $item)
                        <article class="checkout-summary-item">
                            <img src="{{ $item->product?->display_image_url ?? asset('assets/images/new-arrivals/new-1.webp') }}" alt="{{ $item->product_title }}">
                            <div>
                                <strong>{{ $item->product_title }}</strong>
                                <span>{{ $item->attributes['category'] ?? 'Standard item' }}</span>
                                <small>Qty: {{ $item->quantity }}</small>
                            </div>
                            <em>{{ $currencySymbol }}{{ number_format((float) $item->total_price, 2) }}</em>
                        </article>
                    @endforeach
                </div>

                <div class="checkout-summary-totals complete-totals">
                    <div><span>Subtotal</span><strong>{{ $currencySymbol }}{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div><span>Shipping</span><strong>{{ $order->shipping_fee > 0 ? $currencySymbol.number_format((float) $order->shipping_fee, 2) : 'Free' }}</strong></div>
                    <div class="is-total"><span>Total</span><strong>{{ $currencySymbol }}{{ number_format((float) $order->total, 2) }}</strong></div>
                </div>

                <div class="checkout-address-panel">
                    <h2>Shipping Address</h2>
                    <p>{{ $order->shipping_name }}</p>
                    <p>{{ collect([$order->shipping_address, $order->shipping_city, $order->shipping_state, $order->shipping_postal_code, $order->shipping_country])->filter()->implode(', ') }}</p>
                    <p>Phone: {{ $order->phone }}</p>
                    <p>Email: {{ $order->email }}</p>
                </div>
            </div>

            <div class="checkout-complete-actions">
                <a href="{{ route('shop') }}" class="checkout-secondary-link">Continue Shopping</a>
                <a href="{{ route('orders.show', $order) }}" class="checkout-primary-link">View Order Details</a>
            </div>
        </section>
    </div>
</x-layout>
