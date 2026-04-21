@extends('admin.layout')

@section('title', 'Order Detail | Admin')
@section('heading', 'Order Detail')
@section('subheading', 'Review line items, payment state, and shipment tracking on one screen.')

@section('content')
    <div class="two-col">
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>{{ $order->order_number }}</h2>
                    <span class="badge badge-neutral">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="detail-list">
                    <div><span>Customer</span><strong>{{ $order->user?->name ?? $order->shipping_name ?? 'Guest' }}</strong></div>
                    <div><span>Email</span><strong>{{ $order->email ?? $order->user?->email ?? 'N/A' }}</strong></div>
                    <div><span>Payment status</span><strong>{{ ucfirst($order->payment_status) }}</strong></div>
                    <div><span>Fulfillment</span><strong>{{ ucfirst($order->fulfillment_status) }}</strong></div>
                    <div><span>Total</span><strong>${{ number_format($order->total, 2) }}</strong></div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2>Ordered Items</h2></div>
                @if ($order->items->isEmpty())
                    <div class="empty-state">No order items attached yet.</div>
                @else
                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product_title }}</strong>
                                            @if ($item->attributes)
                                                <div class="helper-text">{{ collect($item->attributes)->map(fn ($value, $key) => ucfirst($key).': '.$value)->implode(', ') }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="admin-card">
                <div class="admin-card-header"><h2>Update Order</h2></div>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="admin-form">
                    @csrf
                    @method('PUT')
                    <div class="admin-field">
                        <label>Status</label>
                        <select name="status">
                            @foreach (['pending', 'confirmed', 'cancelled', 'delivered'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label>Payment status</label>
                        <select name="payment_status">
                            @foreach (['unpaid', 'paid', 'failed', 'disputed'] as $status)
                                <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-field">
                        <label>Fulfillment status</label>
                        <select name="fulfillment_status">
                            @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('fulfillment_status', $order->fulfillment_status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admin-form-grid">
                        <div class="admin-field"><label>Carrier</label><input type="text" name="carrier" value="{{ old('carrier', $order->shipment?->carrier) }}"></div>
                        <div class="admin-field"><label>Tracking number</label><input type="text" name="tracking_number" value="{{ old('tracking_number', $order->shipment?->tracking_number) }}"></div>
                    </div>
                    <div class="admin-field"><label>Shipping fee</label><input type="number" step="0.01" min="0" name="shipping_fee" value="{{ old('shipping_fee', $order->shipping_fee) }}"></div>
                    <div class="admin-field"><label>Shipment notes</label><textarea name="shipment_notes">{{ old('shipment_notes', $order->shipment?->notes) }}</textarea></div>
                    <div class="admin-field"><label>Internal notes</label><textarea name="notes">{{ old('notes', $order->notes) }}</textarea></div>
                    <div class="admin-actions">
                        <button type="submit" class="btn btn-primary">Save order</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to orders</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
