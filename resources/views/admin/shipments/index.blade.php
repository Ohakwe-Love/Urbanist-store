@extends('admin.layout')

@section('title', 'Shipping | Admin')
@section('heading', 'Shipping Management')
@section('subheading', 'Track carrier details, tracking numbers, and delivery progress.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search shipments">
                <button class="btn btn-secondary" type="submit">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.shipments.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Carrier</th>
                        <th>Tracking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shipments as $shipment)
                        <tr>
                            <td>{{ $shipment->order?->order_number ?? 'Unlinked' }}</td>
                            <td>{{ $shipment->carrier ?? 'Pending carrier' }}</td>
                            <td>{{ $shipment->tracking_number ?? 'Pending tracking' }}</td>
                            <td><span class="badge badge-warning">{{ ucfirst($shipment->status) }}</span></td>
                            <td><a href="{{ route('admin.shipments.edit', $shipment) }}" class="btn-link">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">Shipment records will show here once orders are being fulfilled.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $shipments->links() }}</div>
    </div>
@endsection
