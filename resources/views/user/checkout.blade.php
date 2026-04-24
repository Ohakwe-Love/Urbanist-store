<x-layout>
    <x-slot name="title">Checkout | Urbanist Store</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    @endpush

    @php
        $user = auth()->user();
        $nameParts = collect(explode(' ', $user->name ?? ''))->filter()->values();
        $firstName = old('first_name', $defaultShippingAddress?->recipient_name ? collect(explode(' ', $defaultShippingAddress->recipient_name))->first() : ($nameParts->first() ?? ''));
        $lastName = old('last_name', $defaultShippingAddress?->recipient_name ? collect(explode(' ', $defaultShippingAddress->recipient_name))->slice(1)->implode(' ') : $nameParts->slice(1)->implode(' '));
    @endphp

    <section class="checkout-hero">
        <div class="checkout-hero-inner">
            <p class="checkout-kicker">Urbanist</p>
            <h1>Checkout</h1>
            <p>Complete your order securely with Paystack.</p>
        </div>
    </section>

    <div class="checkout-shell">
        <div class="checkout-page">
            <div class="checkout-progress" aria-label="Checkout progress">
                <div class="checkout-progress-item is-complete">
                    <span>1</span>
                    <strong>Cart</strong>
                </div>
                <div class="checkout-progress-item is-active">
                    <span>2</span>
                    <strong>Checkout</strong>
                </div>
                <div class="checkout-progress-item">
                    <span>3</span>
                    <strong>Complete</strong>
                </div>
            </div>

            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-grid" id="checkoutForm">
                @csrf

                <section class="checkout-main-card">
                    <div class="checkout-block">
                        <div class="checkout-block-heading">
                            <h2>Customer Information</h2>
                            <p>We’ll use these details for your receipt and delivery updates.</p>
                        </div>

                        @error('cart')
                            <div class="checkout-alert">{{ $message }}</div>
                        @enderror

                        <div class="checkout-form-grid">
                            <div class="checkout-field">
                                <label for="first_name">First name <span>*</span></label>
                                <input type="text" id="first_name" name="first_name" value="{{ $firstName }}" required>
                                @error('first_name') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="last_name">Last name <span>*</span></label>
                                <input type="text" id="last_name" name="last_name" value="{{ $lastName }}" required>
                                @error('last_name') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="email">Email <span>*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="phone">Phone <span>*</span></label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $defaultShippingAddress?->phone ?? $user->phone) }}" required>
                                @error('phone') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="checkout-block">
                        <div class="checkout-block-heading">
                            <h2>Shipping Address</h2>
                            <p>Tell us where you want your order delivered.</p>
                        </div>

                        <div class="checkout-form-grid">
                            <div class="checkout-field checkout-field-full">
                                <label for="address">Address <span>*</span></label>
                                <input type="text" id="address" name="address" value="{{ old('address', $defaultShippingAddress?->address_line_1 ?? $user->address) }}" required>
                                @error('address') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field checkout-field-full">
                                <label for="address_2">Apartment, suite, etc.</label>
                                <input type="text" id="address_2" name="address_2" value="{{ old('address_2', $defaultShippingAddress?->address_line_2) }}">
                                @error('address_2') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="city">City <span>*</span></label>
                                <input type="text" id="city" name="city" value="{{ old('city', $defaultShippingAddress?->city ?? $user->city) }}" required>
                                @error('city') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="state">State <span>*</span></label>
                                <input type="text" id="state" name="state" value="{{ old('state', $defaultShippingAddress?->state ?? $user->state) }}" required>
                                @error('state') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="postal_code">ZIP code <span>*</span></label>
                                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $defaultShippingAddress?->postal_code ?? $user->postal_code) }}" required>
                                @error('postal_code') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="checkout-field">
                                <label for="country">Country <span>*</span></label>
                                <select id="country" name="country" required>
                                    @php
                                        $selectedCountry = old('country', $defaultShippingAddress?->country ?? $user->country ?? 'Nigeria');
                                    @endphp
                                    @foreach (['Nigeria', 'United States', 'Canada', 'United Kingdom', 'Australia'] as $country)
                                        <option value="{{ $country }}" @selected($selectedCountry === $country)>{{ $country }}</option>
                                    @endforeach
                                </select>
                                @error('country') <span class="checkout-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <label class="checkout-check">
                            <input type="checkbox" name="save_address" value="1" @checked(old('save_address'))>
                            <span>Save this address for future checkout</span>
                        </label>
                    </div>

                    <div class="checkout-block">
                        <div class="checkout-block-heading">
                            <h2>Payment Method</h2>
                            <p>You’ll be redirected to Paystack to complete payment securely.</p>
                        </div>

                        <div class="checkout-payment-card">
                            <div class="checkout-payment-badge">Secure</div>
                            <div>
                                <strong>Paystack</strong>
                                <p>Cards, bank transfer, bank app, USSD, and other supported channels are handled on Paystack’s hosted checkout.</p>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-block">
                        <div class="checkout-block-heading">
                            <h2>Order Notes (Optional)</h2>
                            <p>Add delivery notes or anything our team should know.</p>
                        </div>

                        <div class="checkout-field checkout-field-full">
                            <textarea id="order_notes" name="order_notes" placeholder="Special instructions for your order...">{{ old('order_notes') }}</textarea>
                            @error('order_notes') <span class="checkout-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <aside class="checkout-summary-card">
                    <div class="checkout-summary-head">
                        <h2>Order Summary</h2>
                        <a href="{{ route('shop') }}">Continue shopping</a>
                    </div>

                    <div class="checkout-summary-items">
                        @foreach ($cartItems as $item)
                            <article class="checkout-summary-item">
                                <img src="{{ $item->product?->display_image_url ?? asset('assets/images/new-arrivals/new-1.webp') }}" alt="{{ $item->product?->title ?? 'Product' }}">
                                <div>
                                    <strong>{{ $item->product?->title ?? 'Unavailable product' }}</strong>
                                    <span>{{ $item->product?->category ?? 'Standard item' }}</span>
                                    <small>Qty: {{ $item->quantity }}</small>
                                </div>
                                <em>{{ $currencySymbol }}{{ number_format((float) ($item->price * $item->quantity), 2) }}</em>
                            </article>
                        @endforeach
                    </div>

                    <div class="checkout-summary-totals">
                        <div><span>Subtotal</span><strong>{{ $currencySymbol }}{{ number_format((float) $subtotal, 2) }}</strong></div>
                        <div><span>Shipping</span><strong>{{ $shipping > 0 ? $currencySymbol.number_format((float) $shipping, 2) : 'Free' }}</strong></div>
                        @if ($discount > 0)
                            <div><span>Discount</span><strong>-{{ $currencySymbol }}{{ number_format((float) $discount, 2) }}</strong></div>
                        @endif
                        <div class="is-total"><span>Total</span><strong>{{ $currencySymbol }}{{ number_format((float) $total, 2) }}</strong></div>
                    </div>

                    <button type="submit" class="checkout-submit">Proceed To Paystack</button>
                    <p class="checkout-summary-note">By placing your order, you’ll be redirected to Paystack to complete payment and then returned here automatically.</p>
                </aside>
            </form>
        </div>
    </div>
</x-layout>
