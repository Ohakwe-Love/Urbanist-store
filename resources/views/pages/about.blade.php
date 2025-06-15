<x-layout>
    <x-slot name="title">About Urbanist</x-slot>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about.css') }}">
    @endpush


    <!-- About Company Section -->
    <section class="section about-company" id="about">
        <div class="container">
            <div class="about-company-content">
                <div class="about-company-text">
                <h2>Transforming Spaces Into Experiences</h2>
                <p>Founded in 2020, Urbanist is more than just a furniture store—we're a lifestyle destination committed to bringing modern, sustainable design into every home. Our curated collections blend functionality with aesthetic appeal, ensuring that each piece not only serves its purpose but also tells a story.</p>
                <p>At Urbanist, we believe that your living space should be a reflection of your personality and aspirations. That's why we collaborate with talented designers and craftsmen from around the world to create exclusive pieces that stand out and stand the test of time.</p>
                <p>Our mission goes beyond selling furniture—we aim to inspire a conscious approach to modern living, where quality, sustainability, and thoughtful design converge to create spaces that nurture well-being and connection.</p>
            </div>
            <div class="about-company-image">
            <div class="image-grid">
                <div class="image-box">
                <img src="{{asset('assets/images/about-page/img1.webp')}}" alt="Modern living room with Urbanist furniture">
                </div>
                <div class="image-box">
                <img src="{{asset('assets/images/about-page/img2.webp')}}" alt="Sustainable manufacturing process">
                </div>
                <div class="image-box">
                <img src="{{asset('assets/images/about-page/img3.webp')}}" alt="Designer creating furniture sketch">
                </div>
                <div class="image-box">
                <img src="{{asset('assets/images/about-page/img4.webp')}}" alt="Urbanist showroom">
                </div>
            </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="section services" id="services">
        <div class="container">
            <div class="services-intro">
                <h3>Ready to Upgrade Your Living Space?</h3>
                <p>Whether you're furnishing a new home, redesigning a space, or simply looking for that perfect accent piece, Urbanist offers comprehensive services to meet your needs. Our team of design experts is ready to help transform your vision into reality.</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-couch"></i>
                    </div>
                    <h4>Furniture Collections</h4>
                    <p>Explore our extensive range of sofas, tables, chairs, beds, and storage solutions designed with both style and functionality in mind.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h4>Home Decoration</h4>
                    <p>Complete your space with our carefully curated selection of lighting, textiles, wall art, and decorative accessories.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-pencil-ruler"></i>
                    </div>
                    <h4>Interior Design</h4>
                    <p>Work with our professional designers to create a cohesive and personalized look for your entire home or specific rooms.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4>Delivery & Assembly</h4>
                    <p>Enjoy hassle-free delivery and professional assembly services, ensuring your furniture is perfectly set up in your space.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section stats" id="stats">
        <div class="container">
            <div class="stats-intro">
                <h2 class="section-title stats-title">Our Impact</h2>
                <p class="stats-description">
                    At Urbanist, we take pride in making a difference in the lives of our customers and communities. From delivering high-quality furniture to promoting sustainable practices, our goal is to create a lasting impact that goes beyond aesthetics.
                </p>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="customers-count">0</div>
                    <div class="stat-label">Satisfied Customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="furniture-count">0</div>
                    <div class="stat-label">Furniture Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="decorations-count">0</div>
                    <div class="stat-label">Home Decorations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="countries-count">0</div>
                    <div class="stat-label">Countries Served</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="retailers-count">0</div>
                    <div class="stat-label">Partnered Retailers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-description">
                    At Urbanist, we value the voices of our customers. Their stories inspire us to continue delivering exceptional quality, innovative designs, and unparalleled service. Here's what they have to say about their experiences with us.
                </p>
            </div>
            <div class="testimonial-slider">
                <div class="slider-arrows">
                    <div class="arrow prev" onclick="changeSlide(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="arrow next" onclick="changeSlide(1)">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
                
                <div class="testimonial-slide active">
                    <div class="testimonial-content">
                        The quality of Urbanist's furniture is exceptional. I ordered a sectional sofa and coffee table, and both pieces have completely transformed my living room. The designs are modern yet timeless, and the customer service was outstanding from start to finish.
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="{{asset('assets/images/testimonials/avatar1.png')}}" alt="">
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>Interior Designer, New York</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        I've been searching for sustainable furniture options for years, and Urbanist has exceeded all my expectations. Their commitment to eco-friendly materials and ethical manufacturing practices aligns perfectly with my values. Plus, the pieces are gorgeous!
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="{{asset('assets/images/testimonials/avatar2.png')}}" alt="">
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p>Environmental Consultant, San Francisco</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-slide">
                    <div class="testimonial-content">
                        The interior design service at Urbanist was a game-changer for our home renovation. Their designer understood our vision perfectly and helped us create a cohesive look throughout our space. The furniture and decor items are high-quality and unique.
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">
                            <img src="{{asset('assets/images/testimonials/avatar3.png')}}" alt="">
                        </div>
                        <div class="author-info">
                            <h4>Emma & David Rodriguez</h4>
                            <p>Homeowners, Chicago</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-controls">
                    <div class="slider-dots">
                        <div class="dot active" onclick="showSlide(0)"></div>
                        <div class="dot" onclick="showSlide(1)"></div>
                        <div class="dot" onclick="showSlide(2)"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Urbanist Popup -->
    <x-urbanist-popup />
    <!-- urbanist popup -->

    {{-- about.js --}}
    @push('scripts')
        <script src="{{ asset('assets/js/about.js') }}"></script>
    @endpush
</x-layout>