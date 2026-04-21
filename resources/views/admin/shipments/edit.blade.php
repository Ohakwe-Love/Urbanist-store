@extends('admin.layout')

@section('title', 'Edit Shipment | Admin')
@section('heading', 'Edit Shipment')
@section('subheading', 'Keep delivery progress and tracking details current.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-form-grid">
                <div class="admin-field"><label>Carrier</label><input type="text" name="carrier" value="{{ old('carrier', $shipment->carrier) }}"></div>
                <div class="admin-field"><label>Tracking number</label><input type="text" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}"></div>
                <div class="admin-field"><label>Shipping fee</label><input type="number" step="0.01" min="0" name="shipping_fee" value="{{ old('shipping_fee', $shipment->shipping_fee) }}"></div>
            </div>
            <div class="admin-field">
                <label>Status</label>
                <select name="status">
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $shipment->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-field"><label>Notes</label><textarea name="notes">{{ old('notes', $shipment->notes) }}</textarea></div>
            <div class="admin-actions">
                <button type="submit" class="btn btn-primary">Save shipment</button>
                <a href="{{ route('admin.shipments.index') }}" class="btn btn-secondary">Back to shipping</a>
            </div>
        </form>
    </div>
@endsection
