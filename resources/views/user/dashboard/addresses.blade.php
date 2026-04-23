<x-dashboard-layout :user="$user">
    <x-slot name="title">Address Book | Urbanist</x-slot>

    @push('styles')
        <style>
            .dashboard-stack { display:grid; gap:24px; }
            .dashboard-panel { background:#fff; border:1px solid #ece7df; border-radius:20px; padding:24px; }
            .dashboard-panel h2 { margin-bottom:8px; }
            .dashboard-panel p { color:#6e6a64; }
            .address-grid { display:grid; gap:18px; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); margin-top:20px; }
            .address-card { border:1px solid #ece7df; border-radius:18px; padding:18px; background:#faf8f4; }
            .address-card-header { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:12px; }
            .address-badges { display:flex; gap:8px; flex-wrap:wrap; }
            .address-badge { font-size:12px; padding:4px 10px; border-radius:999px; background:#efe7d7; color:#6a4d1f; }
            .address-meta { display:grid; gap:6px; margin-bottom:16px; color:#3d372f; }
            .address-form-grid { display:grid; gap:14px; grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .address-form-grid .span-2 { grid-column:span 2; }
            .address-form-grid label { display:block; font-size:13px; margin-bottom:6px; color:#4d473f; }
            .address-form-grid input { width:100%; padding:12px 14px; border:1px solid #d8d0c4; border-radius:12px; }
            .checkbox-row { display:flex; gap:18px; flex-wrap:wrap; margin-top:6px; }
            .checkbox-row label { display:flex; align-items:center; gap:8px; margin:0; }
            .form-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
            .btn-primary, .btn-secondary, .btn-danger { border:none; border-radius:999px; padding:12px 18px; font-weight:600; cursor:pointer; }
            .btn-primary { background:#1f5f4a; color:#fff; }
            .btn-secondary { background:#ece7df; color:#302b25; }
            .btn-danger { background:#7a2f2f; color:#fff; }
            @media (max-width: 768px) {
                .address-form-grid { grid-template-columns:1fr; }
                .address-form-grid .span-2 { grid-column:span 1; }
            }
        </style>
    @endpush

    <div class="dashboard-header">
        <div>
            <h1>Address Book</h1>
            <p class="welcome-subtext">Save delivery and billing addresses for faster checkout.</p>
        </div>

        <x-dashboard-sidebar-toggle />
    </div>

    <div class="dashboard-stack">
        <section class="dashboard-panel">
            <h2>Add a new address</h2>
            <p>Keep multiple addresses on file and mark your preferred defaults.</p>

            <form action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="address-form-grid">
                    <div>
                        <label for="label">Label</label>
                        <input id="label" name="label" type="text" value="{{ old('label') }}" placeholder="Home, Office, Studio" required>
                    </div>
                    <div>
                        <label for="recipient_name">Recipient name</label>
                        <input id="recipient_name" name="recipient_name" type="text" value="{{ old('recipient_name', $user->name) }}" required>
                    </div>
                    <div class="span-2">
                        <label for="address_line_1">Address line 1</label>
                        <input id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1') }}" required>
                    </div>
                    <div class="span-2">
                        <label for="address_line_2">Address line 2</label>
                        <input id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2') }}" placeholder="Apartment, suite, landmark">
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div>
                        <label for="postal_code">Postal code</label>
                        <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code') }}">
                    </div>
                    <div>
                        <label for="city">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" required>
                    </div>
                    <div>
                        <label for="state">State / Province</label>
                        <input id="state" name="state" type="text" value="{{ old('state') }}">
                    </div>
                    <div class="span-2">
                        <label for="country">Country</label>
                        <input id="country" name="country" type="text" value="{{ old('country', $user->country) }}" required>
                    </div>
                </div>

                <div class="checkbox-row">
                    <label><input type="checkbox" name="is_default_shipping" value="1" @checked(old('is_default_shipping'))> Default shipping address</label>
                    <label><input type="checkbox" name="is_default_billing" value="1" @checked(old('is_default_billing'))> Default billing address</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save address</button>
                </div>
            </form>
        </section>

        <section class="dashboard-panel">
            <h2>Saved addresses</h2>
            <p>Update or remove addresses any time. Defaults are used automatically at checkout.</p>

            @if ($addresses->isEmpty())
                <div class="address-card" style="margin-top:20px;">
                    <strong>No addresses yet.</strong>
                    <p style="margin-top:8px;">Add your first address above to speed up delivery and billing details during checkout.</p>
                </div>
            @else
                <div class="address-grid">
                    @foreach ($addresses as $address)
                        <article class="address-card">
                            <div class="address-card-header">
                                <div>
                                    <strong>{{ $address->label }}</strong>
                                    <div style="color:#6e6a64; margin-top:4px;">{{ $address->recipient_name }}</div>
                                </div>
                                <div class="address-badges">
                                    @if ($address->is_default_shipping)
                                        <span class="address-badge">Default shipping</span>
                                    @endif
                                    @if ($address->is_default_billing)
                                        <span class="address-badge">Default billing</span>
                                    @endif
                                </div>
                            </div>

                            <div class="address-meta">
                                <span>{{ $address->full_address }}</span>
                                @if ($address->phone)
                                    <span>{{ $address->phone }}</span>
                                @endif
                            </div>

                            <form action="{{ route('addresses.update', $address) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="address-form-grid">
                                    <div>
                                        <label for="label-{{ $address->id }}">Label</label>
                                        <input id="label-{{ $address->id }}" name="label" type="text" value="{{ old("label.{$address->id}", $address->label) }}" required>
                                    </div>
                                    <div>
                                        <label for="recipient_name-{{ $address->id }}">Recipient name</label>
                                        <input id="recipient_name-{{ $address->id }}" name="recipient_name" type="text" value="{{ old("recipient_name.{$address->id}", $address->recipient_name) }}" required>
                                    </div>
                                    <div class="span-2">
                                        <label for="address_line_1-{{ $address->id }}">Address line 1</label>
                                        <input id="address_line_1-{{ $address->id }}" name="address_line_1" type="text" value="{{ old("address_line_1.{$address->id}", $address->address_line_1) }}" required>
                                    </div>
                                    <div class="span-2">
                                        <label for="address_line_2-{{ $address->id }}">Address line 2</label>
                                        <input id="address_line_2-{{ $address->id }}" name="address_line_2" type="text" value="{{ old("address_line_2.{$address->id}", $address->address_line_2) }}">
                                    </div>
                                    <div>
                                        <label for="phone-{{ $address->id }}">Phone</label>
                                        <input id="phone-{{ $address->id }}" name="phone" type="text" value="{{ old("phone.{$address->id}", $address->phone) }}">
                                    </div>
                                    <div>
                                        <label for="postal_code-{{ $address->id }}">Postal code</label>
                                        <input id="postal_code-{{ $address->id }}" name="postal_code" type="text" value="{{ old("postal_code.{$address->id}", $address->postal_code) }}">
                                    </div>
                                    <div>
                                        <label for="city-{{ $address->id }}">City</label>
                                        <input id="city-{{ $address->id }}" name="city" type="text" value="{{ old("city.{$address->id}", $address->city) }}" required>
                                    </div>
                                    <div>
                                        <label for="state-{{ $address->id }}">State / Province</label>
                                        <input id="state-{{ $address->id }}" name="state" type="text" value="{{ old("state.{$address->id}", $address->state) }}">
                                    </div>
                                    <div class="span-2">
                                        <label for="country-{{ $address->id }}">Country</label>
                                        <input id="country-{{ $address->id }}" name="country" type="text" value="{{ old("country.{$address->id}", $address->country) }}" required>
                                    </div>
                                </div>

                                <div class="checkbox-row">
                                    <label><input type="checkbox" name="is_default_shipping" value="1" @checked($address->is_default_shipping)> Default shipping</label>
                                    <label><input type="checkbox" name="is_default_billing" value="1" @checked($address->is_default_billing)> Default billing</label>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-secondary">Update</button>
                                </div>
                            </form>

                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" style="margin-top:10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" onclick="return confirm('Remove this address from your account?')">Delete</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-dashboard-layout>
