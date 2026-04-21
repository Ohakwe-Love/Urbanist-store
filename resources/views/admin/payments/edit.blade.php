@extends('admin.layout')

@section('title', 'Edit Payment | Admin')
@section('heading', 'Edit Payment')
@section('subheading', 'Confirm successful payments, flag disputes, and update references.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-form-grid">
                <div class="admin-field"><label>Reference</label><input type="text" name="payment_reference" value="{{ old('payment_reference', $payment->payment_reference) }}"></div>
                <div class="admin-field"><label>Method</label><input type="text" name="method" value="{{ old('method', $payment->method) }}"></div>
                <div class="admin-field">
                    <label>Status</label>
                    <select name="status">
                        @foreach (['pending', 'successful', 'failed', 'disputed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $payment->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="admin-field"><label>Notes</label><textarea name="notes">{{ old('notes', $payment->notes) }}</textarea></div>
            <div class="admin-actions">
                <button type="submit" class="btn btn-primary">Save payment</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Back to payments</a>
            </div>
        </form>
    </div>
@endsection
