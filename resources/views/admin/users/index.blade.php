@extends('admin.layout')

@section('title', 'Users | Admin')
@section('heading', 'User Management')
@section('subheading', 'Review customer accounts, admin permissions, and account status.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users">
                <button class="btn btn-secondary" type="submit">Search</button>
            </form>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last login</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <div class="helper-text">{{ $user->email }}</div>
                            </td>
                            <td><span class="badge badge-neutral">{{ ucfirst($user->role ?? 'customer') }}</span></td>
                            <td><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                            <td><a href="{{ route('admin.users.edit', $user) }}" class="btn-link">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state">No users found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $users->links() }}</div>
    </div>
@endsection
