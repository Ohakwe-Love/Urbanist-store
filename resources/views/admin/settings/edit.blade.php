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
                <div class="admin-field"><label>Business hours</label><input type="text" name="business_hours" value="{{ old('business_hours', $settings['business_hours'] ?? '') }}"></div>
            </div>
            <div class="admin-field"><label>Address</label><textarea name="address">{{ old('address', $settings['address'] ?? '') }}</textarea></div>
            <div class="admin-form-grid">
                <div class="admin-field"><label>Facebook URL</label><input type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"></div>
                <div class="admin-field"><label>Pinterest URL</label><input type="url" name="pinterest_url" value="{{ old('pinterest_url', $settings['pinterest_url'] ?? '') }}"></div>
                <div class="admin-field"><label>Instagram URL</label><input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"></div>
                <div class="admin-field"><label>Twitter URL</label><input type="url" name="twitter_url" value="{{ old('twitter_url', $settings['twitter_url'] ?? '') }}"></div>
                <div class="admin-field"><label>TikTok URL</label><input type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url'] ?? '') }}"></div>
            </div>
            <div class="admin-field"><label>Payment gateway keys / notes</label><textarea name="payment_gateway_keys">{{ old('payment_gateway_keys', $settings['payment_gateway_keys'] ?? '') }}</textarea></div>
            <div class="admin-field"><label>Shipping settings / notes</label><textarea name="shipping_settings">{{ old('shipping_settings', $settings['shipping_settings'] ?? '') }}</textarea></div>
            <div class="admin-actions"><button class="btn btn-primary" type="submit">Save settings</button></div>
        </form>
    </div>
@endsection
