@extends('admin.layout')

@section('title', 'Edit Tag | Admin')
@section('heading', 'Edit Tag')
@section('subheading', 'Update a tag used to group products and campaigns.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-field"><label>Name</label><input type="text" name="name" value="{{ old('name', $tag->name) }}" required></div>
            <div class="admin-field"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $tag->slug) }}"></div>
            <div class="admin-actions">
                <button class="btn btn-primary" type="submit">Save tag</button>
                <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
