@extends('admin.layout')

@section('title', 'Content | Admin')
@section('heading', 'Content Management')
@section('subheading', 'Keep banners, promos, about copy, and contact content current.')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <form method="GET" class="admin-inline-actions">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search content blocks">
                <button class="btn btn-secondary" type="submit">Search</button>
                @if (request('search'))
                    <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Block</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blocks as $block)
                        <tr>
                            <td>
                                <strong>{{ $block->title }}</strong>
                                <div class="helper-text">{{ $block->key }}</div>
                            </td>
                            <td><span class="badge {{ $block->is_active ? 'badge-success' : 'badge-danger' }}">{{ $block->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td><a href="{{ route('admin.content.edit', $block) }}" class="btn-link">Edit block</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><div class="empty-state">No content blocks match this search yet.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $blocks->links() }}</div>
    </div>
@endsection
