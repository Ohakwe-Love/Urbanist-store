@extends('admin.layout')

@section('title', 'Coupons | Admin')
@section('heading', 'Coupon Management')
@section('subheading', 'Control discounts, expiry windows, and campaign availability.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h2>Coupons</h2>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">Create coupon</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>{{ ucfirst($coupon->type) }}</td>
                            <td>{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '$'.number_format($coupon->value, 2) }}</td>
                            <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? 'Unlimited' }}</td>
                            <td><span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <div class="admin-actions">
                                    <a class="btn-link" href="{{ route('admin.coupons.edit', $coupon) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-link" type="submit" onclick="return confirm('Delete this coupon?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state">No coupons yet. Create one for your next campaign.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $coupons->links() }}</div>
    </div>
@endsection
