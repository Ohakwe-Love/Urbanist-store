@extends('admin.layout')

@section('title', 'Categories | Admin')
@section('heading', 'Category Management')
@section('subheading', 'Organize the storefront into clean browsing buckets.')

@section('content')
    <div class="two-col">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Create category</h2>
            </div>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="admin-form">
                @csrf
                <div class="admin-field"><label>Name</label><input type="text" name="name" required></div>
                <div class="admin-field"><label>Slug</label><input type="text" name="slug"></div>
                <div class="admin-field"><label>Description</label><textarea name="description"></textarea></div>
                <div class="admin-field"><label><input type="checkbox" name="is_active" value="1" checked> Active category</label></div>
                <div class="admin-actions"><button class="btn btn-primary" type="submit">Create category</button></div>
            </form>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Existing categories</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                    <div class="helper-text">{{ $category->slug }}</div>
                                </td>
                                <td>{{ $category->products_count }}</td>
                                <td><span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <div class="admin-actions">
                                        <a class="btn-link" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-link" type="submit" onclick="return confirm('Delete this category?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="empty-state">No categories created yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection
