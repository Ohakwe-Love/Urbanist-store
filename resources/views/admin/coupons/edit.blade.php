@extends('admin.layout')

@section('title', 'Edit Coupon | Admin')
@section('heading', 'Edit Coupon')
@section('subheading', 'Adjust the campaign terms without losing visibility of the current setup.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="admin-form">
            @csrf
            @method('PUT')
            @include('admin.coupons._form', ['submitLabel' => 'Save coupon'])
        </form>
    </div>
@endsection
