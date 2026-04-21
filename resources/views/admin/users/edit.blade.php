@extends('admin.layout')

@section('title', 'Edit User | Admin')
@section('heading', 'Edit User')
@section('subheading', 'Control access level and keep suspicious accounts contained.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-form-grid">
                <div class="admin-field"><label>Name</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
                <div class="admin-field"><label>Username</label><input type="text" name="username" value="{{ old('username', $user->username) }}"></div>
                <div class="admin-field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                <div class="admin-field">
                    <label>Role</label>
                    <select name="role">
                        <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                </div>
            </div>
            <div class="admin-field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))> Account is active</label></div>
            <div class="admin-actions">
                <button class="btn btn-primary" type="submit">Save user</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to users</a>
            </div>
        </form>
    </div>
@endsection
