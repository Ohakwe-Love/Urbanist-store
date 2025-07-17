<x-layout>
    @push ('styles')
        <link rel="stylesheet" href="{{asset('assets/css/cookies.css')}}">
    @endpush

    <div class="cookies-container">
        <div class="cookies-header">
            <h1>Cookies Policy</h1>
            {{-- <p>Last updated: May 21, 2025</p> --}}
        </div>

        <div class="cookies-section">
            <h2>What are cookies?</h2>
            <p>Cookies are small text files that are placed on your device when you visit our website. They allow us to recognize your device and remember certain information about your session, such as your preferences and the pages you've visited.</p>
            <p>We use cookies to improve your browsing experience, analyze site traffic, personalize content, and serve targeted advertisements. By using our website, you consent to our use of cookies as described in this policy.</p>
        </div>

        <div class="cookies-section">
            <h2>Types of cookies we use</h2>
            <p>We use different types of cookies for various purposes. Some cookies are essential for the functioning of our website, while others help us improve our services and your shopping experience.</p>

            <div class="cookies-types">
                <div class="cookie-type">
                    <h3>Essential Cookies</h3>
                    <p>These cookies are necessary for the website to function properly. They enable basic functions like page navigation, secure areas access, and shopping cart functionality. The website cannot function properly without these cookies.</p>
                </div>

                <div class="cookie-type">
                    <h3>Preference Cookies</h3>
                    <p>These cookies allow the website to remember choices you make (such as your preferred language or the region you are in) and provide enhanced, more personalized features.</p>
                </div>

                <div class="cookie-type">
                    <h3>Analytics Cookies</h3>
                    <p>These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. This helps us improve the way our website works.</p>
                </div>

                <div class="cookie-type">
                    <h3>Marketing Cookies</h3>
                    <p>These cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging to the individual user and thereby more valuable for publishers and third-party advertisers.</p>
                </div>
            </div>
        </div>

        <div class="cookies-section">
            <h2>Cookies we use</h2>
            <p>Below is a detailed list of the cookies we use on our website:</p>

            <table>
                <thead>
                    <tr>
                        <th>Cookie Name</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>_session</td>
                        <td>Essential</td>
                        <td>Maintains your session state and remembers items in your shopping cart</td>
                        <td>Session</td>
                    </tr>
                    <tr>
                        <td>user_preferences</td>
                        <td>Preference</td>
                        <td>Remembers your preferred currency, language, and display settings</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>_ga</td>
                        <td>Analytics</td>
                        <td>Used by Google Analytics to distinguish users</td>
                        <td>2 years</td>
                    </tr>
                    <tr>
                        <td>_gid</td>
                        <td>Analytics</td>
                        <td>Used by Google Analytics to distinguish users</td>
                        <td>24 hours</td>
                    </tr>
                    <tr>
                        <td>_fbp</td>
                        <td>Marketing</td>
                        <td>Used by Facebook to deliver a series of advertisement products</td>
                        <td>3 months</td>
                    </tr>
                    <tr>
                        <td>_ads</td>
                        <td>Marketing</td>
                        <td>Used to track visitor behavior for targeted advertising</td>
                        <td>3 months</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cookies-section">
            <h2>How to manage cookies</h2>
            <p>Most web browsers allow some control of most cookies through the browser settings. To find out more about cookies, including how to see what cookies have been set, visit <a href="https://www.aboutcookies.org">www.aboutcookies.org</a> or <a href="https://www.allaboutcookies.org">www.allaboutcookies.org</a>.</p>
            
            <p>You can also manage your cookie preferences directly on our website using the Cookie Preferences option below.</p>
        </div>

        <div class="cookie-preferences">
            <h2>Cookie Preferences</h2>
            <p>You can adjust your cookie preferences here. Please note that disabling some types of cookies may impact your experience on our site and the services we are able to offer.</p>

            <div class="cookie-settings">
                <div class="cookie-toggle">
                    <div class="cookie-toggle-text">
                        <strong>Essential Cookies</strong>
                        <span>Always active and necessary for the website to work properly</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked disabled>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="cookie-toggle">
                    <div class="cookie-toggle-text">
                        <strong>Preference Cookies</strong>
                        <span>Allows the website to remember your preferences</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="cookie-toggle">
                    <div class="cookie-toggle-text">
                        <strong>Analytics Cookies</strong>
                        <span>Helps us understand how you use our website</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="cookie-toggle">
                    <div class="cookie-toggle-text">
                        <strong>Marketing Cookies</strong>
                        <span>Used to deliver personalized advertisements</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="cookie-buttons">
                <button class="btn btn-outline">Reject All</button>
                <button class="btn btn-primary">Accept Selected</button>
                <button class="btn btn-primary">Accept All</button>
            </div>
        </div>

        <div class="contact-info">
            <p>If you have any questions about our use of cookies, please contact us at:</p>
            <p>Email: <a href="mailto:privacy@yourstore.com">privacy@yourstore.com</a></p>
            <p>Phone: +1 (555) 123-4567</p>
        </div>

        <div class="last-updated">
            {{-- <p>This Cookie Policy was last updated on May 21, 2025</p> --}}
        </div>
    </div>

    @push ('scripts')
        <script src="{{asset('assets/js/cookies.js')}}"></script>
    @endpush
</x-layout>
