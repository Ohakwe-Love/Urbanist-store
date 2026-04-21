<div class="admin-form-grid">
    <div class="admin-field"><label>Coupon code</label><input type="text" name="code" value="{{ old('code', $coupon->code) }}" required></div>
    <div class="admin-field">
        <label>Discount type</label>
        <select name="type">
            <option value="percentage" @selected(old('type', $coupon->type) === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed amount</option>
        </select>
    </div>
    <div class="admin-field"><label>Value</label><input type="number" step="0.01" min="0" name="value" value="{{ old('value', $coupon->value) }}" required></div>
    <div class="admin-field"><label>Expiry date</label><input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}"></div>
    <div class="admin-field"><label>Usage limit</label><input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"></div>
    <div class="admin-field"><label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active))> Coupon is active</label></div>
</div>

<div class="admin-actions">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back to coupons</a>
</div>
