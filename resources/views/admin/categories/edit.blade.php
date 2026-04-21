@extends('admin.layout')

@section('title', 'Edit Category | Admin')
@section('heading', 'Edit Category')
@section('subheading', 'Update how products are grouped in the storefront.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-field"><label>Name</label><input type="text" name="name" value="{{ old('name', $category->name) }}" required></div>
            <div class="admin-field"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $category->slug) }}"></div>
            <div class="admin-field"><label>Description</label><textarea name="description">{{ old('description', $category->description) }}</textarea></div>
            <div class="admin-field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Active category</label></div>
            <div class="admin-actions">
                <button class="btn btn-primary" type="submit">Save category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection
