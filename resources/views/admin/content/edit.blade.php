@extends('admin.layout')

@section('title', 'Edit Content Block | Admin')
@section('heading', 'Edit Content Block')
@section('subheading', 'Update the copy that powers your key marketing sections.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.content.update', $block) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-field"><label>Title</label><input type="text" name="title" value="{{ old('title', $block->title) }}" required></div>
            <div class="admin-field"><label>Content</label><textarea name="content">{{ old('content', $block->content) }}</textarea></div>
            <div class="admin-field"><label>Meta notes</label><textarea name="meta">{{ old('meta', $block->meta['notes'] ?? '') }}</textarea></div>
            <div class="admin-field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $block->is_active))> Block is active</label></div>
            <div class="admin-actions">
                <button type="submit" class="btn btn-primary">Save block</button>
                <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">Back to content</a>
            </div>
        </form>
    </div>
@endsection
