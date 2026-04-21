@extends('admin.layout')

@section('title', 'Payments | Admin')
@section('heading', 'Payment Monitoring')
@section('subheading', 'Track successful, failed, and disputed payments in one queue.')

@section('content')
    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Order</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_reference ?? 'Pending reference' }}</td>
                            <td>{{ $payment->order?->order_number ?? 'Unlinked' }}</td>
                            <td>{{ $payment->method ?? 'Unknown' }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td><span class="badge badge-neutral">{{ ucfirst($payment->status) }}</span></td>
                            <td><a href="{{ route('admin.payments.edit', $payment) }}" class="btn-link">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">Payments will appear here when they are recorded.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $payments->links() }}</div>
    </div>
@endsection
