<x-layout>
    <x-slot name="title">Returns || Urbanist Stores Return Policies</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{asset('assets/css/returns.css')}}">
    @endpush

    <div class="returns-container">
        <h1 class="page-title">Returns & Refunds</h1>
        
        <p class="returns-intro">
            We want you to be completely satisfied with your purchase. If you're not happy with your order for any reason, we're here to help you with an easy and hassle-free return process.
        </p>
        
        <div class="returns-steps">
            <div class="step-card">
                <div class="step-number">1</div>
                <h2 class="step-title">Request Return</h2>
                <p class="step-description">
                    Fill out our simple return form below or contact our customer service team to initiate your return request within 30 days of receiving your order.
                </p>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <h2 class="step-title">Package Items</h2>
                <p class="step-description">
                    Carefully pack the unused items in their original packaging with all tags attached. Include your order number or the return authorization form.
                </p>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <h2 class="step-title">Ship Back</h2>
                <p class="step-description">
                    Send your package using your preferred shipping method or use our prepaid return label if eligible. Keep the tracking number for reference.
                </p>
            </div>
            
            <div class="step-card">
                <div class="step-number">4</div>
                <h2 class="step-title">Get Refunded</h2>
                <p class="step-description">
                    Once we receive and inspect your return, we'll process your refund to your original payment method within 5-7 business days.
                </p>
            </div>
        </div>
        
        <div class="returns-policy">
            <h2 class="policy-title">Our Return Policy</h2>
            
            <div class="policy-item">
                <h3>Return Eligibility</h3>
                <p>Items are eligible for return within 30 days of delivery, provided they are:</p>
                <ul class="policy-list">
                    <li>In original, unused condition with all tags attached</li>
                    <li>In original packaging</li>
                    <li>Not marked as final sale or clearance items</li>
                    <li>Not personal hygiene products that have been opened or used</li>
                </ul>
            </div>
            
            <div class="policy-item">
                <h3>Refund Process</h3>
                <p>
                    Refunds will be issued to your original payment method once your return is received and inspected. Please allow 5-7 business days for the refund to appear in your account. Shipping costs are non-refundable unless the return is due to our error.
                </p>
            </div>
            
            <div class="policy-item">
                <h3>Exchanges</h3>
                <p>
                    We currently don't offer direct exchanges. If you need a different size or color, please return the original item for a refund and place a new order for the desired item.
                </p>
            </div>
            
            <div class="policy-item">
                <h3>Damaged or Incorrect Items</h3>
                <p>
                    If you receive damaged or incorrect items, please contact us within 48 hours of delivery. Include photos of the damaged item or packaging, and we'll arrange for a replacement or refund.
                </p>
            </div>
        </div>
        
        <div class="returns-form">
            <h2 class="form-title">Return Request Form</h2>
            
            <form id="returnForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="orderNumber">Order Number</label>
                        <input type="text" id="orderNumber" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="returnReason">Reason for Return</label>
                    <select id="returnReason" class="form-control" required>
                        <option value="" selected disabled>Select a reason</option>
                        <option value="wrong-size">Wrong Size</option>
                        <option value="defective">Defective/Damaged Item</option>
                        <option value="not-as-described">Not as Described</option>
                        <option value="wrong-item">Received Wrong Item</option>
                        <option value="changed-mind">Changed My Mind</option>
                        <option value="better-price">Found Better Price Elsewhere</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="itemsToReturn">Items to Return</label>
                    <textarea id="itemsToReturn" class="form-control" placeholder="Please list the items you wish to return, including item names, quantities, and any SKU/product codes if available." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="additionalInfo">Additional Comments (Optional)</label>
                    <textarea id="additionalInfo" class="form-control" placeholder="Any additional information you'd like us to know?"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Upload Images (Optional)</label>
                    <div class="file-upload">
                        <span class="upload-icon">📷</span>
                        <span class="file-upload-text">Drop images here or click to upload</span>
                        <input type="file" id="returnImages" accept="image/*" multiple>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-center">Submit Return Request</button>
            </form>
        </div>
        
        <div class="faq-section">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            
            <div class="accordion">
                <div class="accordion-header">How long do I have to return an item?</div>
                <div class="accordion-content">
                    <p>You have 30 days from the delivery date to initiate a return. After this period, returns may be considered on a case-by-case basis but are not guaranteed.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Do I have to pay for return shipping?</div>
                <div class="accordion-content">
                    <p>Yes, customers are responsible for return shipping costs unless the return is due to our error (wrong item sent, defective product, etc.). In those cases, we'll provide a prepaid return label.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">How long will it take to process my refund?</div>
                <div class="accordion-content">
                    <p>Once we receive your return, it typically takes 1-2 business days to inspect and process. After processing, refunds take 5-7 business days to appear in your account, depending on your payment provider.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">Can I return sale or discounted items?</div>
                <div class="accordion-content">
                    <p>Yes, most sale items can be returned following our standard return policy. However, items marked as "Final Sale" or "Clearance" are not eligible for returns or refunds.</p>
                </div>
            </div>
            
            <div class="accordion">
                <div class="accordion-header">What if my item arrives damaged?</div>
                <div class="accordion-content">
                    <p>If your item arrives damaged, please contact us within 48 hours with photos of the damage. We'll arrange for a replacement or full refund, including shipping costs. You won't need to return damaged items in most cases.</p>
                </div>
            </div>
        </div>
        
        <div class="contact-box">
            <h2 class="contact-title">Need Help With Your Return?</h2>
            <p class="contact-text">Our customer service team is available to assist you with any questions or concerns about returns and refunds.</p>
            <p>
                <a href="mailto:support@yourstore.com" class="contact-link">support@yourstore.com</a> | 
                <a href="tel:+18001234567" class="contact-link">1-800-123-4567</a>
            </p>
        </div>
    </div>


    {{-- script --}}
    @push ('scripts')
        <script src="{{asset('assets/js/returns.js')}}"></script>
    @endpush
</x-layout>
