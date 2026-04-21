@extends('admin.layout')

@section('title', 'Settings | Admin')
@section('heading', 'Store Settings')
@section('subheading', 'Control the core business identity, contact details, and integration notes.')

@section('content')
    <div class="admin-card">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form">
            @csrf
            @method('PUT')
            <div class="admin-form-grid">
                <div class="admin-field"><label>Store name</label><input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? '') }}" required></div>
                <div class="admin-field"><label>Logo path</label><input type="text" name="logo_path" value="{{ old('logo_path', $settings['logo_path'] ?? '') }}"></div>
                <div class="admin-field"><label>Contact email</label><input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" required></div>
                <div class="admin-field"><label>Phone number</label><input type="text" name="phone_number" value="{{ old('phone_number', $settings['phone_number'] ?? '') }}"></div>
            </div>
            <div class="admin-field"><label>Address</label><textarea name="address">{{ old('address', $settings['address'] ?? '') }}</textarea></div>
            <div class="admin-field"><label>Payment gateway keys / notes</label><textarea name="payment_gateway_keys">{{ old('payment_gateway_keys', $settings['payment_gateway_keys'] ?? '') }}</textarea></div>
            <div class="admin-field"><label>Shipping settings / notes</label><textarea name="shipping_settings">{{ old('shipping_settings', $settings['shipping_settings'] ?? '') }}</textarea></div>
            <div class="admin-actions"><button class="btn btn-primary" type="submit">Save settings</button></div>
        </form>
    </div>
@endsection
