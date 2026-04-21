@extends('admin.layout')

@section('title', 'Add Product | Admin')
@section('heading', 'Add Product')
@section('subheading', 'Create a new product, upload images, and define its merchandising details.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="admin-form">
            @csrf
            @include('admin.products._form', ['submitLabel' => 'Create product'])
        </form>
    </div>
@endsection
