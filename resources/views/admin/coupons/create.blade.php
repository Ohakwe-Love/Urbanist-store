@extends('admin.layout')

@section('title', 'Create Coupon | Admin')
@section('heading', 'Create Coupon')
@section('subheading', 'Launch a new promotional code with rules and expiry limits.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="admin-form">
            @csrf
            @include('admin.coupons._form', ['submitLabel' => 'Create coupon'])
        </form>
    </div>
@endsection
