@extends('admin.layout')

@section('title', 'Tags | Admin')
@section('heading', 'Tag Management')
@section('subheading', 'Create supporting tags for merchandising, campaigns, and searchability.')

@section('content')
    <div class="two-col">
        <div class="admin-card">
            <div class="admin-card-header"><h2>Create tag</h2></div>
            <form method="POST" action="{{ route('admin.tags.store') }}" class="admin-form">
                @csrf
                <div class="admin-field"><label>Name</label><input type="text" name="name" required></div>
                <div class="admin-field"><label>Slug</label><input type="text" name="slug"></div>
                <div class="admin-actions"><button class="btn btn-primary" type="submit">Create tag</button></div>
            </form>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h2>Existing tags</h2>
                <form method="GET" class="admin-inline-actions">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tags">
                    <button class="btn btn-secondary" type="submit">Search</button>
                    @if (request('search'))
                        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Products</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tags as $tag)
                            <tr>
                                <td>
                                    <strong>{{ $tag->name }}</strong>
                                    <div class="helper-text">{{ $tag->slug }}</div>
                                </td>
                                <td>{{ $tag->products_count }}</td>
                                <td>
                                    <div class="admin-actions">
                                        <a class="btn-link" href="{{ route('admin.tags.edit', $tag) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-link" type="submit" onclick="return confirm('Delete this tag?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><div class="empty-state">No tags created yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $tags->links() }}</div>
        </div>
    </div>
@endsection
