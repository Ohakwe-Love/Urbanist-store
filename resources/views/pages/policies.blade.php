<x-layout>
    <x-slot name="title">Policies | Urbanist Store Policies</x-slot>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/policies.css') }}">
    @endpush

    <div class="policies-container">
        <div class="policies-header">
            <h1>Our Policies</h1>
            <p>We value transparency and want to ensure you understand how we operate. Please take a moment to review our policies regarding purchases, returns, privacy, and more.</p>
        </div>

        <div class="search-policies">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Search our policies...">
        </div>

        <div class="policies-nav">
            <div class="policy-nav-item active">Terms & Conditions</div>
            <div class="policy-nav-item">Privacy Policy</div>
            <div class="policy-nav-item">Shipping Policy</div>
            <div class="policy-nav-item">Return Policy</div>
            <div class="policy-nav-item">Payment Policy</div>
        </div>

        <div class="policy-section" id="terms">
            <h2>Terms & Conditions</h2>
            <p>Welcome to our website. If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use.</p>

            <h3>Agreement to Terms</h3>
            <p>By accessing or using our website, you agree to be bound by these Terms and all applicable laws and regulations. If you do not agree with any of these terms, you are prohibited from using or accessing this site.</p>

            <div class="policy-highlight">
                <p><span class="info-icon">i</span> These Terms constitute a legally binding agreement made between you and our company concerning your access to and use of our website.</p>
            </div>

            <h3>Intellectual Property</h3>
            <p>The content, organization, graphics, design, compilation, magnetic translation, digital conversion, and other matters related to the Website are protected under applicable copyrights, trademarks, and other proprietary rights.</p>
            <p>The copying, redistribution, use, or publication by you of any such content or any part of the Website is strictly prohibited. You do not acquire ownership rights to any content on our Website.</p>

            <h3>User Accounts</h3>
            <p>When you create an account with us, you must provide accurate, complete, and current information at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our service.</p>
            <p>You are responsible for safeguarding the password you use to access the service and for any activities or actions under your password. You agree not to disclose your password to any third party.</p>

            <h3>Product Information</h3>
            <p>We strive to provide accurate product information, but we do not warrant that product descriptions or other content is accurate, complete, reliable, current, or error-free. If a product offered by us is not as described, your sole remedy is to return it in unused condition.</p>

            <h3>Limitation of Liability</h3>
            <p>In no event shall we be liable for any direct, indirect, incidental, special, consequential or exemplary damages, including but not limited to, damages for loss of profits, goodwill, use, data or other intangible losses resulting from the use of or inability to use the service.</p>

            <div class="accordion">
                <div class="accordion-header">Governing Law</div>
                <div class="accordion-content">
                    <p>These Terms shall be governed and construed in accordance with the laws applicable in your jurisdiction, without regard to its conflict of law provisions.</p>
                    <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect.</p>
                </div>
            </div>

            <div class="accordion">
                <div class="accordion-header">Changes to Terms</div>
                <div class="accordion-content">
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days' notice prior to any new terms taking effect.</p>
                    <p>By continuing to access or use our service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the service.</p>
                </div>
            </div>

            <div class="policy-contact">
                <h3>Contact Us</h3>
                <p>If you have any questions about these Terms, please contact us at:</p>
                <p>Email: <a href="mailto:legal@yourstore.com">legal@yourstore.com</a></p>
                <p>Phone: +1 (555) 123-4567</p>
            </div>
        </div>

        <div class="policy-section" id="privacy">
            <h2>Privacy Policy</h2>
            <p>Your privacy is important to us. It is our policy to respect your privacy regarding any information we may collect from you across our website.</p>

            <h3>Information We Collect</h3>
            <p>We only collect information about you if we have a reason to do so — for example, to provide our services, to communicate with you, or to make our services better. We collect this information from three sources:</p>
            <ul>
                <li>Information you provide to us (name, email address, billing and shipping information)</li>
                <li>Information we collect automatically (IP address, browser information, device information)</li>
                <li>Information we get from third parties (analytics providers, advertising partners)</li>
            </ul>

            <h3>How We Use Your Information</h3>
            <p>We use the information we collect in various ways, including to:</p>
            <ul>
                <li>Provide, operate, and maintain our website</li>
                <li>Improve, personalize, and expand our website</li>
                <li>Understand and analyze how you use our website</li>
                <li>Develop new products, services, features, and functionality</li>
                <li>Communicate with you to provide updates and marketing messages</li>
                <li>Process your transactions</li>
                <li>Find and prevent fraud</li>
            </ul>

            <div class="policy-highlight">
                <p><span class="info-icon">i</span> We will never sell your personal data to third parties for marketing purposes.</p>
            </div>

            <h3>Cookies</h3>
            <p>We use cookies and similar tracking technologies to track the activity on our website and hold certain information. Cookies are files with a small amount of data which may include an anonymous unique identifier.</p>
            <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.</p>

            <h3>Data Security</h3>
            <p>The security of your data is important to us, but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your personal data, we cannot guarantee its absolute security.</p>

            <div class="accordion">
                <div class="accordion-header">Your Data Protection Rights</div>
                <div class="accordion-content">
                    <p>Depending on your location, you may have the following data protection rights:</p>
                    <ul>
                        <li>The right to access, update or delete your personal information</li>
                        <li>The right of rectification</li>
                        <li>The right to object to processing</li>
                        <li>The right of restriction</li>
                        <li>The right to data portability</li>
                        <li>The right to withdraw consent</li>
                    </ul>
                </div>
            </div>

            <div class="policy-contact">
                <h3>Contact Us</h3>
                <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                <p>Email: <a href="mailto:privacy@yourstore.com">privacy@yourstore.com</a></p>
                <p>Phone: +1 (555) 123-4567</p>
            </div>
        </div>

        <div class="policy-section" id="shipping">
            <h2>Shipping Policy</h2>
            <p>We aim to provide fast, reliable shipping on all orders. Please review our shipping policy to understand delivery times, shipping costs, and international shipping information.</p>

            <h3>Processing Time</h3>
            <p>All orders are processed within 1-3 business days (excluding weekends and holidays) after receiving your order confirmation email. You will receive another notification when your order has shipped.</p>

            <h3>Shipping Rates & Delivery Times</h3>
            <p>Shipping rates are calculated based on the weight of your order, dimensions of the package, and your location. You can calculate your shipping cost by adding items to your cart and proceeding to checkout.</p>

            <table>
                <thead>
                    <tr>
                        <th>Shipping Method</th>
                        <th>Delivery Time</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Standard Shipping</td>
                        <td>5-7 business days</td>
                        <td>$4.99 - $9.99</td>
                    </tr>
                    <tr>
                        <td>Expedited Shipping</td>
                        <td>3-5 business days</td>
                        <td>$12.99 - $19.99</td>
                    </tr>
                    <tr>
                        <td>Express Shipping</td>
                        <td>1-2 business days</td>
                        <td>$24.99 - $39.99</td>
                    </tr>
                    <tr>
                        <td>Free Shipping</td>
                        <td>5-10 business days</td>
                        <td>Free on orders over $75</td>
                    </tr>
                </tbody>
            </table>

            <div class="policy-highlight">
                <p><span class="info-icon">i</span> Orders over $75 qualify for free standard shipping within the continental United States (excludes Alaska and Hawaii).</p>
            </div>

            <h3>International Shipping</h3>
            <p>We ship to most countries worldwide. International delivery times typically range from 7-21 business days depending on the destination country and customs processing.</p>
            <p>Please note that import duties, taxes, and charges are not included in the item price or shipping charges. These charges are the buyer's responsibility.</p>

            <h3>Tracking Information</h3>
            <p>Once your order ships, you will receive a shipping confirmation email with a tracking number. You can track your package by clicking the tracking link in the email or entering the tracking number on the carrier's website.</p>

            <div class="accordion">
                <div class="accordion-header">Shipping Delays & Issues</div>
                <div class="accordion-content">
                    <p>Although we make every effort to ship orders according to the estimated delivery times, delays may occur due to carrier delays, weather conditions, or other unforeseen circumstances.</p>
                    <p>If you haven't received your order within the expected timeframe, please check your tracking information first. If there's an issue with your delivery, please contact our customer service team.</p>
                </div>
            </div>

            <div class="policy-contact">
                <h3>Contact Us</h3>
                <p>If you have any questions about shipping, please contact us at:</p>
                <p>Email: <a href="mailto:shipping@yourstore.com">shipping@yourstore.com</a></p>
                <p>Phone: +1 (555) 123-4567</p>
            </div>
        </div>

        <div class="policy-section" id="returns">
            <h2>Return Policy</h2>
            <p>We want you to be completely satisfied with your purchase. If you're not happy with your order for any reason, we're here to help.</p>

            <h3>Return Eligibility</h3>
            <p>Items can be returned within 30 days of delivery for a full refund. To be eligible for a return, your item must be:</p>
            <ul>
                <li>In the same condition that you received it</li>
                <li>Unworn, unused, and with all original tags attached</li>
                <li>In the original packaging</li>
            </ul>

            <h3>Non-Returnable Items</h3>
            <p>The following items cannot be returned:</p>
            <ul>
                <li>Gift cards</li>
                <li>Downloadable products</li>
                <li>Personalized items</li>
                <li>Intimate apparel</li>
                <li>Sale items (marked "Final Sale")</li>
            </ul>

            <div class="policy-highlight">
                <p><span class="info-icon">i</span> For hygiene reasons, we cannot accept returns on opened cosmetics, intimate apparel, or earrings.</p>
            </div>

            <h3>Return Process</h3>
            <p>To initiate a return, please follow these steps:</p>
            <ol>
                <li>Contact our customer service team at <a href="mailto:returns@yourstore.com">returns@yourstore.com</a> with your order number and return reason</li>
                <li>You will receive a Return Merchandise Authorization (RMA) number and return instructions</li>
                <li>Package your item securely and include the RMA number on the outside of the package</li>
                <li>Ship your return to the address provided in the return instructions</li>
            </ol>

            <h3>Refunds</h3>
            <p>Once we receive and inspect your return, we'll send you an email to notify you that we've received your returned item. We'll also notify you of the approval or rejection of your refund.</p>
            <p>If approved, your refund will be processed, and a credit will automatically be applied to your original method of payment within 5-10 business days.</p>

            <div class="accordion">
                <div class="accordion-header">Damaged or Defective Items</div>
                <div class="accordion-content">
                    <p>If you receive a damaged or defective product, please contact us immediately at <a href="mailto:support@yourstore.com">support@yourstore.com</a> with photos of the damage and your order number.</p>
                    <p>We will arrange for a return shipping label and send a replacement or issue a full refund at our discretion. Damaged or defective items should be reported within 7 days of receipt.</p>
                </div>
            </div>

            <div class="policy-contact">
                <h3>Contact Us</h3>
                <p>If you have any questions about our return policy, please contact us at:</p>
                <p>Email: <a href="mailto:returns@yourstore.com">returns@yourstore.com</a></p>
                <p>Phone: +1 (555) 123-4567</p>
            </div>
        </div>

        <div class="policy-section" id="payment">
            <h2>Payment Policy</h2>
            <p>We offer several secure payment methods to make your shopping experience convenient and safe.</p>

            <h3>Accepted Payment Methods</h3>
            <p>We accept the following payment methods:</p>
            <ul>
                <li>Credit Cards: Visa, MasterCard, American Express, Discover</li>
                <li>Debit Cards</li>
                <li>PayPal</li>
                <li>Apple Pay</li>
                <li>Google Pay</li>
                <li>Shop Pay</li>
                <li>Gift Cards</li>
            </ul>

            <h3>Payment Security</h3>
            <p>All payments are processed through secure payment gateways. We use industry-standard encryption technology to protect your personal and financial information.</p>
            <p>We never store your full credit card details on our servers. All transactions are handled by our PCI-compliant payment processors.</p>

            <div class="policy-highlight">
                <p><span class="info-icon">i</span> Your payment information is encrypted using SSL technology and we never store your complete card details on our servers.</p>
            </div>

            <h3>Order Processing</h3>
            <p>Your credit card will be charged at the time you place your order. If we are unable to fulfill your order for any reason, we will issue a full refund to your original payment method.</p>

            <h3>Currency</h3>
            <p>All prices on our website are displayed in US Dollars (USD). International customers may be charged in their local currency depending on their payment method and bank.</p>

            <div class="accordion">
                <div class="accordion-header">Payment Issues</div>
                <div class="accordion-content">
                    <p>If your payment is declined, please verify that your billing information is correct and that your card has sufficient funds.</p>
                    <p>For security reasons, some banks may block online transactions. If you continue to experience issues, please contact your bank or try another payment method.</p>
                </div>
            </div>

            <div class="policy-contact">
                <h3>Contact Us</h3>
                <p>If you have any questions about our payment policy, please contact us at:</p>
                <p>Email: <a href="mailto:billing@yourstore.com">billing@yourstore.com</a></p>
                <p>Phone: +1 (555) 123-4567</p>
            </div>
        </div>

        <div class="last-updated">
            <p>Last updated: May 21, 2025</p>
        </div>
    </div>

    @push('scripts')
       <script src="{{ asset('assets/js/policies.js') }}"></script>
    @endpush
</x-layout>