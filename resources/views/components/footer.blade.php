<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>CONTACT US</h3>
            <p>Morbi ullamcorper ligula sit amet efficitur pellentesque. Aliquam ornare quam tellus ultricies molestie tortor.</p>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div class="contact-text-hotline">
                        <p class="hotline">HOTLINE :</p>
                        <p>+123-456-789</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p class="contact-text">info@example.com</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-calendar"></i>
                    <p class="contact-text">Monday till Friday 10 to 6 EST</p>
                </div>
            </div>
        </div>
    
        <div class="footer-section">
            <h3>HELP</h3>
            <ul class="footer-links">
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Shipping Info</a></li>
            <li><a href="{{route('returns')}}">Returns</a></li>
            <li><a href="{{route('how-to-order')}}">How To Order</a></li>
            <li><a href="{{route(name: 'cookies')}}">Cookies</a></li>
            <li><a href="{{route('policies')}}">Policies</a></li>
            </ul>
        </div>
    
        <div class="footer-section">
            <h3>COMPANY</h3>
            <ul class="footer-links">
                <li><a href="{{route('about')}}">About Us</a></li>
                <li><a href="{{route('offer')}}">Offers</a></li>
                <li><a href="{{route('news')}}">News Update </a></li>
                <li><a href="#">Store Locations</a></li>
                <li><a href="#">Testimonial</a></li>
                <li><a href="#">Sitemap</a></li>
            </ul>
        </div>
    
        <div class="footer-section">
            <h3>NEWSLETTER</h3>
            <p>Get 15% off your first purchases! Plus, be the first to know about sales new product launches and exclusive offers!</p>
            <div class="newsletter-form">
                <input type="email" placeholder="Your Email Address">
                <button type="submit"><i class="fas fa-arrow-right"></i></button>
            </div>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-pinterest-p"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>Copyright &copy; <span class="">{{date('Y')}}</span> Website by Lovely_girl. All rights reserved.</p>
    </div>
</footer>