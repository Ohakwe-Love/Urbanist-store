<x-layout>
    <x-slot name="title">Urbanist || How to Order Products</x-slot>

    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/how-to-order.css')}}?v={{ time() }}">
    @endpush

    <div class="order-container">
        <h1 class="page-title">How To Order</h1>
        <p class="page-subtitle">
            We've made ordering from our store simple and convenient. Follow this step-by-step guide to make your shopping experience smooth and hassle-free.
        </p>
        
        <!-- Hero Section -->
        <div class="hero-section animate-fade-in-up">
            <h2 class="hero-title">Ready to Place Your Order?</h2>
            <p class="hero-subtitle">
                Shopping with us is quick, easy, and secure. Follow the simple steps below to find your perfect products and complete your purchase.
            </p>
            <a href="{{route('shop')}}" class="hero-btn">Shop Now</a>
        </div>
        
        <!-- Order Steps -->
        <div class="steps-section">
            <h2 class="section-title">Our Simple Ordering Process</h2>
            
            <div class="steps-container">
                <!-- Step 1 -->
                <div class="step-item animate-fade-in-up">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">Browse Our Products</h3>
                        <p class="step-description">
                            Explore our extensive catalog of products. Use the search bar or navigate through our categories to find exactly what you're looking for.
                        </p>
                        <div class="step-tips">
                            <h4 class="step-tips-title">Browsing Tips:</h4>
                            <ul class="step-tips-list">
                                <li>Use filters to narrow down your search by price, category, or features</li>
                                <li>Check out our "Featured Products" section for popular items</li>
                                <li>View product ratings and reviews from other customers</li>
                            </ul>
                        </div>
                        <img src="{{asset('assets/images/order-steps/step1.png')}}" alt="Browse Products" class="step-image">
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="step-item animate-fade-in-up">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Add Items to Your Cart</h3>
                        <p class="step-description">
                            Once you've found the perfect item, select your desired options (size, color, quantity, etc.) and click the "Add to Cart" button. Your item will be saved in your shopping cart.
                        </p>
                        <div class="step-tips">
                            <h4 class="step-tips-title">Cart Tips:</h4>
                            <ul class="step-tips-list">
                                <li>Items in your cart are saved for 30 days</li>
                                <li>You can always edit quantities or remove items from your cart</li>
                                <li>Check for any available discount codes to apply at checkout</li>
                            </ul>
                        </div>
                        <img src="{{asset('assets/images/order-steps/step2.png')}}" alt="Add to Cart" class="step-image">
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="step-item animate-fade-in-up">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">Proceed to Checkout</h3>
                        <p class="step-description">
                            When you're ready to complete your purchase, click on the shopping cart icon and select "Proceed to Checkout". Review your order details before continuing.
                        </p>
                        <div class="step-tips">
                            <h4 class="step-tips-title">Checkout Tips:</h4>
                            <ul class="step-tips-list">
                                <li>Double-check your items and quantities</li>
                                <li>Apply any promo codes in the designated field</li>
                                <li>Estimate shipping costs and delivery times</li>
                            </ul>
                        </div>
                        <img src="/api/placeholder/600/320" alt="Review Cart" class="step-image">
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="step-item animate-fade-in-up">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">Enter Shipping Information</h3>
                        <p class="step-description">
                            Provide your shipping address and select your preferred shipping method. We offer various shipping options to meet your needs.
                        </p>
                        <div class="step-tips">
                            <h4 class="step-tips-title">Shipping Tips:</h4>
                            <ul class="step-tips-list">
                                <li>Create an account to save your shipping details for future orders</li>
                                <li>Consider expedited shipping for faster delivery</li>
                                <li>Add special delivery instructions if needed</li>
                            </ul>
                        </div>
                        <img src="/api/placeholder/600/320" alt="Shipping Information" class="step-image">
                    </div>
                </div>
                
                <!-- Step 5 -->
                <div class="step-item animate-fade-in-up">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3 class="step-title">Payment & Confirmation</h3>
                        <p class="step-description">
                            Enter your payment information and complete your purchase. We accept various payment methods for your convenience. After submission, you'll receive an order confirmation via email.
                        </p>
                        <div class="step-tips">
                            <h4 class="step-tips-title">Payment Tips:</h4>
                            <ul class="step-tips-list">
                                <li>All payment information is encrypted and secure</li>
                                <li>Check your email for order confirmation</li>
                                <li>Create an account to easily track your order status</li>
                            </ul>
                        </div>
                        <img src="/api/placeholder/600/320" alt="Payment" class="step-image">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Methods -->
        <div class="payment-methods animate-fade-in-up">
            <h3 class="payment-title">Accepted Payment Methods</h3>
            <div class="payment-icons">
                <div class="payment-icon">Visa</div>
                <div class="payment-icon">MasterCard</div>
                <div class="payment-icon">PayPal</div>
                <div class="payment-icon">Apple Pay</div>
                <div class="payment-icon">Google Pay</div>
                <div class="payment-icon">Shop Pay</div>
            </div>
            <p class="payment-description">
                We offer various secure payment options to suit your preferences. All transactions are encrypted and protected.
            </p>
        </div>
        
        <!-- Video Tutorial -->
        <div class="video-section animate-fade-in-up">
            <h3 class="video-title">Watch Our Order Tutorial</h3>
            <div class="video-container">
                <div class="video-placeholder">
                    <div class="play-button">
                        <div class="play-icon"></div>
                    </div>
                </div>
                <div class="video-caption">
                    Watch our step-by-step video guide on how to place an order on our website
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section animate-fade-in-up">
            <h2 class="section-title">Frequently Asked Questions</h2>
            
            <div class="accordion">
                <div class="accordion-header">How long will it take to receive my order?</div>
                <div class="accordion-content">
                    <p>Delivery times depend on your location and the shipping method you choose. Standard shipping typically takes 3-7 business days, while expedited shipping can deliver your order in 1-3 business days. International orders may take 7-14 business days. You can view estimated delivery times during checkout before placing your order.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Can I modify or cancel my order after it's been placed?</div>
                <div class="accordion-content">
                    <p>You can modify or cancel your order within 1 hour of placing it. After that, orders enter our processing system and cannot be changed. To request modifications or cancellations within the first hour, please contact our customer service team immediately via phone or live chat for the fastest assistance.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Do you offer international shipping?</div>
                <div class="accordion-content">
                    <p>Yes, we ship to most countries worldwide. International shipping rates and delivery times vary by location. Please note that international orders may be subject to customs duties, taxes, or import fees imposed by the destination country. These additional charges are the responsibility of the recipient and are not included in our shipping costs.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Is it safe to use my credit card on your website?</div>
                <div class="accordion-content">
                    <p>Absolutely! Your security is our top priority. Our website uses SSL encryption technology to protect your personal and payment information. We are PCI DSS compliant and never store your full credit card details on our servers. Additionally, we offer secure payment options like PayPal and Apple Pay, which provide extra layers of security for your transactions.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Do I need to create an account to place an order?</div>
                <div class="accordion-content">
                    <p>No, you can place an order as a guest without creating an account. However, creating an account offers several benefits: faster checkout on future orders, ability to track your orders, access to order history, and eligibility for loyalty rewards. Account creation is quick, requiring only your email address and a password.</p>
                </div>
            </div>
        </div>
        
        <!-- Need Help Section -->
        <div class="help-section animate-fade-in-up">
            <h3 class="help-title">Need Assistance?</h3>
            <p class="help-text">
                Our dedicated customer service team is ready to help you with any questions about ordering or navigating our website. We're available 24/7 to ensure your shopping experience is smooth and enjoyable.
            </p>
            <div>
                <a href="#" class="help-btn">Live Chat</a>
                <a href="#" class="help-btn">Contact Us</a>
            </div>
            <p>
                Email us at <a href="mailto:support@yourstore.com" class="help-link">support@yourstore.com</a> or call <a href="tel:+18001234567" class="help-link">1-800-123-4567</a>
            </p>
        </div>
    </div>

    @push ('scripts')
        <script src="{{asset('assets/css/how-to-order.css')}}?v={{ time() }}"></script>
    @endpush
</x-layout>