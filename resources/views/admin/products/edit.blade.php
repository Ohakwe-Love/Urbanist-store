@extends('admin.layout')

@section('title', 'Edit Product | Admin')
@section('heading', 'Edit Product')
@section('subheading', 'Update inventory, pricing, media, and merchandising details.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="admin-form">
            @csrf
            @method('PUT')
            @include('admin.products._form', ['submitLabel' => 'Save changes'])
        </form>
    </div>
@endsection
