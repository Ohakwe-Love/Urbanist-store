@extends('admin.layout')

@section('title', 'Newsletter Subscribers | Admin')
@section('heading', 'Newsletter Subscribers')
@section('subheading', 'Track subscribers collected from the storefront newsletter form.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscribers">
                <button class="btn btn-secondary" type="submit">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.newsletters.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
            <span class="helper-text">{{ $subscribers->total() }} total subscribers</span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Subscribed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscribers as $subscriber)
                        <tr>
                            <td>{{ $subscriber->email }}</td>
                            <td>
                                <span class="badge {{ $subscriber->is_active ? 'badge-success' : 'badge-warning' }}">
                                    {{ $subscriber->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ ($subscriber->subscribed_at ?? $subscriber->created_at)?->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><div class="empty-state">No newsletter subscribers yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $subscribers->links() }}</div>
    </div>
@endsection
