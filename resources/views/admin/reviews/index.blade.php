@extends('admin.layout')

@section('title', 'Reviews | Admin')
@section('heading', 'Review Management')
@section('subheading', 'Approve genuine feedback and remove spam or abusive submissions.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reviews">
                <button class="btn btn-secondary" type="submit">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Review</th>
                        <th>Product</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <strong>{{ $review->title ?? 'Untitled review' }}</strong>
                                <div class="helper-text">{{ \Illuminate\Support\Str::limit($review->body, 90) }}</div>
                            </td>
                            <td>{{ $review->product?->title ?? 'Removed product' }}</td>
                            <td>{{ $review->user?->name ?? 'Guest' }}</td>
                            <td><span class="badge {{ $review->is_approved ? 'badge-success' : 'badge-warning' }}">{{ $review->is_approved ? 'Approved' : 'Pending' }}</span></td>
                            <td>
                                <div class="admin-actions">
                                    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                        <button class="btn-link" type="submit">{{ $review->is_approved ? 'Unapprove' : 'Approve' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-link" type="submit" onclick="return confirm('Delete this review?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No reviews have been submitted yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $reviews->links() }}</div>
    </div>
@endsection
