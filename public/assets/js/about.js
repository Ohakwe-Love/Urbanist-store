// Counter animation for stats
function animateCounter(elementId, finalNumber, duration) {
    const element = document.getElementById(elementId);
    const increment = finalNumber / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
    current += increment;
    if (current >= finalNumber) {
        clearInterval(timer);
        current = finalNumber;
    }
    element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}

// Intersection Observer to trigger counter animation when in view
const statsSection = document.getElementById('stats');
const statsObserver = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
    animateCounter('customers-count', 25000, 2000);
    animateCounter('furniture-count', 5000, 2000);
    animateCounter('decorations-count', 3500, 2000);
    animateCounter('countries-count', 45, 2000);
    animateCounter('retailers-count', 120, 2000);
    statsObserver.unobserve(statsSection);
    }
}, { threshold: 0.5 });

statsObserver.observe(statsSection);

// Testimonial slider
let currentSlide = 0;
const slides = document.querySelectorAll('.testimonial-slide');
const dots = document.querySelectorAll('.dot');

function showSlide(n) {
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    currentSlide = n;
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

function changeSlide(moveBy) {
    let newIndex = currentSlide + moveBy;
    
    if (newIndex < 0) {
    newIndex = slides.length - 1;
    } else if (newIndex >= slides.length) {
    newIndex = 0;
    }
    
    showSlide(newIndex);
}

// Auto-rotate testimonials
setInterval(() => {
    changeSlide(1);
}, 6000);