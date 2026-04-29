<x-dashboard-layout :user="$user">
    <x-slot name="title">Payments | Urbanist</x-slot>

    @push('styles')
        <style>
            .payments-panel { background:#fff; border:1px solid #ece7df; border-radius:20px; padding:24px; }
            .payments-table-wrap { overflow-x:auto; margin-top:18px; }
            .payments-table { width:100%; border-collapse:collapse; }
            .payments-table th, .payments-table td { padding:14px 12px; border-bottom:1px solid #ece7df; text-align:left; font-size:14px; }
            .payments-table th { color:#645e56; text-transform:uppercase; letter-spacing:.04em; font-size:12px; }
            .payment-badge { display:inline-flex; align-items:center; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
            .payment-badge.success { background:#dff4e8; color:#1f6a46; }
            .payment-badge.pending { background:#f7ead1; color:#8a5a00; }
            .payment-badge.disputed { background:#f8dddd; color:#9d3030; }
            .payment-link { color:#1f5f4a; text-decoration:none; font-weight:600; }
        </style>
    @endpush

    <div class="dashboard-header">
        <div>
            <h1>Payments</h1>
            <p class="welcome-subtext">Track completed, pending, and disputed payments tied to your orders.</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

    <div class="payments-panel">
        <div class="welcome-header">
            <div>
                <h2 class="welcome-title">Payment History</h2>
                <p class="welcome-subtext">Every checkout payment tied to your account appears here.</p>
            </div>
        </div>

        @if ($payments->isEmpty())
            <div class="empty-state">
                <p>No payment records yet. Once you complete checkout, your transactions will show here.</p>
            </div>
        @else
            <div class="payments-table-wrap">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Order</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            @php($order = $payment->order)
                            <tr>
                                <td>{{ $payment->payment_reference ?? 'Pending reference' }}</td>
                                <td>{{ $order?->order_number ?? 'Removed order' }}</td>
                                <td>{{ ucfirst($payment->method ?? $payment->gateway ?? 'paystack') }}</td>
                                <td>
                                    <span class="payment-badge {{
                                        $payment->status === 'successful' ? 'badge-success'
                                        : ($payment->status === 'disputed' ? 'badge-danger' : 'badge-warning')
                                    }} {{
                                        $payment->status === 'successful' ? 'success'
                                        : ($payment->status === 'disputed' ? 'disputed' : 'pending')
                                    }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $order?->currency_symbol ?? '$' }}{{ number_format((float) $payment->amount, 2) }}</td>
                                <td>{{ $payment->paid_at?->format('M j, Y') ?? 'Not yet' }}</td>
                                <td>
                                    @if ($order)
                                        <a href="{{ route('orders.show', $order) }}" class="payment-link">View order</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination" style="margin-top:20px;">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-dashboard-layout>
