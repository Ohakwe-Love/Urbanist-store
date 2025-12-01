<x-layout>

    <x-slot name="title">Checkout - Urbanist Store</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    @endpush
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <div class="checkout-steps">
                <div class="step">
                    <span class="step-number">1</span>
                    <span>Shopping Cart</span>
                </div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span>Checkout Details</span>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span>Order Complete</span>
                </div>
            </div>
        </div>

        <form action="" method="POST" id="checkoutForm">
            @csrf
            <div class="checkout-content">
                <div class="checkout-form">
                    <!-- Contact Information -->
                    <div class="form-section">
                        <h2>Contact Information</h2>
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email"
                                value=" old('email', auth()->user()->email ?? '')" required>
                            @error('email')
                                <span class="error-message"> $message</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" value=" old('phone', auth()->user()->phone ?? '')"
                                required>
                            @error('phone')
                                <span class="error-message"> $message</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="form-section">
                        <h2>Shipping Address</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" value=" old('first_name')"
                                    required>
                                @error('first_name')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" value=" old('last_name')" required>
                                @error('last_name')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address <span class="required">*</span></label>
                            <input type="text" id="address" name="address" value=" old('address')" required>
                            @error('address')
                                <span class="error-message"> $message</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address_2">Apartment, Suite, etc. (Optional)</label>
                            <input type="text" id="address_2" name="address_2" value=" old('address_2')">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City <span class="required">*</span></label>
                                <input type="text" id="city" name="city" value=" old('city')" required>
                                @error('city')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="state">State/Province <span class="required">*</span></label>
                                <input type="text" id="state" name="state" value=" old('state')" required>
                                @error('state')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="postal_code">Postal Code <span class="required">*</span></label>
                                <input type="text" id="postal_code" name="postal_code" value=" old('postal_code')"
                                    required>
                                @error('postal_code')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="country">Country <span class="required">*</span></label>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US" old('country')=='US' ? 'selected' : ''>United States</option>
                                    <option value="CA" old('country')=='CA' ? 'selected' : ''>Canada</option>
                                    <option value="UK" old('country')=='UK' ? 'selected' : ''>United Kingdom
                                    </option>
                                    <option value="AU" old('country')=='AU' ? 'selected' : ''>Australia</option>
                                    <option value="NG" old('country')=='NG' ? 'selected' : ''>Nigeria</option>
                                </select>
                                @error('country')
                                    <span class="error-message"> $message</span>
                                @enderror
                            </div>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="save_address" name="save_address" value="1">
                            <label for="save_address">Save this address for next time</label>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="form-section">
                        <h2>Billing Address</h2>
                        <div class="checkbox-group" style="margin-top: 0; margin-bottom: 15px;">
                            <input type="checkbox" id="same_as_shipping" name="same_as_shipping" value="1" checked>
                            <label for="same_as_shipping">Same as shipping address</label>
                        </div>
                        <div id="billing_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="billing_first_name">First Name</label>
                                    <input type="text" id="billing_first_name" name="billing_first_name"
                                        value=" old('billing_first_name')">
                                </div>
                                <div class="form-group">
                                    <label for="billing_last_name">Last Name</label>
                                    <input type="text" id="billing_last_name" name="billing_last_name"
                                        value=" old('billing_last_name')">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="billing_address">Street Address</label>
                                <input type="text" id="billing_address" name="billing_address"
                                    value=" old('billing_address')">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="billing_city">City</label>
                                    <input type="text" id="billing_city" name="billing_city"
                                        value=" old('billing_city')">
                                </div>
                                <div class="form-group">
                                    <label for="billing_postal_code">Postal Code</label>
                                    <input type="text" id="billing_postal_code" name="billing_postal_code"
                                        value=" old('billing_postal_code')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <label class="payment-option selected">
                                <input type="radio" name="payment_method" value="card" checked>
                                <span>Credit/Debit Card</span>
                            </label>
                            <div class="payment-details active" id="card_details">
                                <div class="form-group">
                                    <label for="card_number">Card Number <span class="required">*</span></label>
                                    <input type="text" id="card_number" name="card_number"
                                        placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="expiry_date">Expiry Date <span class="required">*</span></label>
                                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv">CVV <span class="required">*</span></label>
                                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="paypal">
                                <span>PayPal</span>
                            </label>
                            <div class="payment-details" id="paypal_details">
                                <p style="font-size: var(--text-small-font-size); color: var(--color-dark-gray);">
                                    You will be redirected to PayPal to complete your purchase.
                                </p>
                            </div>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bank_transfer">
                                <span>Bank Transfer</span>
                            </label>
                            <div class="payment-details" id="bank_details">
                                <p
                                    style="font-size: var(--text-small-font-size); color: var(--color-dark-gray); margin-bottom: 10px;">
                                    Transfer payment to our bank account. Your order will be processed after payment
                                    confirmation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="form-section">
                        <h2>Order Notes (Optional)</h2>
                        <div class="form-group">
                            <label for="order_notes">Special instructions or delivery notes</label>
                            <textarea id="order_notes" name="order_notes"
                                placeholder="Any special requests or delivery instructions..."> old('order_notes')</textarea>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="order-items">
                        @forelse($cartItems ?? [] as $item)
                            <div class="order-item">
                                <img src=" $item->product->image ?? '/images/placeholder.jpg'" alt=" $item->product->name"
                                    class="item-image">
                                <div class="item-details">
                                    <div class="item-name"> $item->product->name</div>
                                    <div class="item-variant"> $item->variant ?? 'Default'</div>
                                    <div class="item-quantity">Qty: $item->quantity</div>
                                </div>
                                <div class="item-price">$ number_format($item->price * $item->quantity, 2)</div>
                            </div>
                        @empty
                            <div class="order-item">
                                <img src="/images/placeholder.jpg" alt="Product" class="item-image">
                                <div class="item-details">
                                    <div class="item-name">Urbanist T-Shirt</div>
                                    <div class="item-variant">Size: L, Color: Black</div>
                                    <div class="item-quantity">Qty: 2</div>
                                </div>
                                <div class="item-price">$59.98</div>
                            </div>
                            <div class="order-item">
                                <img src="/images/placeholder.jpg" alt="Product" class="item-image">
                                <div class="item-details">
                                    <div class="item-name">Urbanist Hoodie</div>
                                    <div class="item-variant">Size: M, Color: Navy</div>
                                    <div class="item-quantity">Qty: 1</div>
                                </div>
                                <div class="item-price">$79.99</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="discount-code">
                        <div class="discount-input-group">
                            <input type="text" id="discount_code" name="discount_code" placeholder="Discount code"
                                value=" old('discount_code')">
                            <button type="button" class="btn-apply" onclick="applyDiscount()">Apply</button>
                        </div>
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span id="subtotal">$ number_format($subtotal ?? 139.97, 2)</span>
                        </div>
                        <div class="total-row">
                            <span>Shipping</span>
                            <span id="shipping">$ number_format($shipping ?? 10.00, 2)</span>
                        </div>
                        <div class="total-row">
                            <span>Tax</span>
                            <span id="tax">$ number_format($tax ?? 12.00, 2)</span>
                        </div>
                        @if(isset($discount) && $discount > 0)
                            <div class="total-row discount">
                                <span>Discount</span>
                                <span id="discount">-$ number_format($discount, 2)</span>
                            </div>
                        @endif
                        <div class="total-row final">
                            <span>Total</span>
                            <span id="total">$ number_format($total ?? 161.97, 2)</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Place Order</button>
                    <div class="secure-checkout">
                        <span class="secure-icon">🔒</span>
                        Secure Checkout
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Toggle billing address fields
        document.getElementById('same_as_shipping').addEventListener('change', function () {
            const billingFields = document.getElementById('billing_fields');
            billingFields.style.display = this.checked ? 'none' : 'block';
        });

        // Payment method selection
        document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function () {
                // Remove selected class from all options
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });

                // Add selected class to parent label
                this.closest('.payment-option').classList.add('selected');

                // Hide all payment details
                document.querySelectorAll('.payment-details').forEach(detail => {
                    detail.classList.remove('active');
                });

                // Show selected payment details
                const paymentType = this.value;
                const detailsId = paymentType === 'card' ? 'card_details' :
                    paymentType === 'paypal' ? 'paypal_details' :
                        'bank_details';
                document.getElementById(detailsId).classList.add('active');
            });
        });

        // Apply discount code
        function applyDiscount() {
            const code = document.getElementById('discount_code').value;
            if (code.trim() === '') {
                alert('Please enter a discount code');
                return;
            }

            // Make AJAX request to apply discount
            fetch(' route("checkout.apply-discount")', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': ' csrf_token()'
                },
                body: JSON.stringify({ code: code })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update totals
                        document.getElementById('subtotal').textContent = '$' + data.subtotal.toFixed(2);
                        document.getElementById('shipping').textContent = '$' + data.shipping.toFixed(2);
                        document.getElementById('tax').textContent = '$' + data.tax.toFixed(2);
                        document.getElementById('total').textContent = '$' + data.total.toFixed(2);

                        alert('Discount applied successfully!');
                    } else {
                        alert(data.message || 'Invalid discount code');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error applying discount code');
                });
        }

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (paymentMethod === 'card') {
                const cardNumber = document.getElementById('card_number').value;
                const expiryDate = document.getElementById('expiry_date').value;
                const cvv = document.getElementById('cvv').value;

                if (!cardNumber || !expiryDate || !cvv) {
                    e.preventDefault();
                    alert('Please fill in all card details');
                    return false;
                }
            }
        });

        // Format card number input
        document.getElementById('card_number')?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date
        document.getElementById('expiry_date')?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>

</x-layout>