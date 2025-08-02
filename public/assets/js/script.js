document.addEventListener('DOMContentLoaded', () => {
    const header = document.getElementById('header');

    const isHomePage = window.location.pathname === '/';
    const isAboutPage = window.location.pathname === '/about';

    if (!isHomePage) {
        // Apply background color and set position to relative
        header.style.backgroundColor = 'var(--secondary-color) !important';
        header.style.position = 'relative';
        header.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.1)';
    }
});

// preloader
window.addEventListener('load', () => {
    const loader = document.querySelector('.pre-loader-container');
    loader.style.opacity = '0';
    loader.style.pointerEvents = 'none';
    setTimeout(() => loader.remove(), 1000);
});

// Unhiding the dropdown with single open logic

let currentlyOpenDropdown = null;

const clickedbtns = document.querySelectorAll('.clicked');

clickedbtns.forEach(clickedbtn => {
    clickedbtn.addEventListener('click', function () {
        const dropdown = clickedbtn.nextElementSibling;

        if (dropdown) {
            // Close the currently open dropdown if it's not the same as the clicked one
            if (currentlyOpenDropdown && currentlyOpenDropdown !== dropdown) {
                currentlyOpenDropdown.classList.remove('open');
            }

            // Toggle the clicked dropdown
            if (!dropdown.classList.contains('open')) {
                dropdown.classList.add('open');
                currentlyOpenDropdown = dropdown;
            } else {
                dropdown.classList.remove('open');
                currentlyOpenDropdown = null;
            }
        }
    });
});

// Search functionality
const searchTrigger = document.getElementById('search-trigger');
const searchPopup = document.getElementById('search-popup');
const closeSearch = document.getElementById('close-search');

searchTrigger.addEventListener('click', () => {
    searchPopup.classList.add('active');
    document.body.style.overflow = 'hidden';
});

closeSearch.addEventListener('click', () => {
    searchPopup.classList.remove('active');
    document.body.style.overflow = 'auto';
});

// Mobile menu functionality
const mobileMenuTrigger = document.querySelector('.mobile-menu-trigger');
const mobileMenu = document.getElementById('mobile-menu');
const mobileMenuClose = document.getElementById('mobile-menu-close');
const overlay = document.getElementById('overlay');

mobileMenuTrigger.addEventListener('click', () => {
    mobileMenu.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    console.log('clicked');
    
});

mobileMenuClose.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
});


// Quantity buttons
// const minusBtns = document.querySelectorAll('.quantity-btn.minus');
// const plusBtns = document.querySelectorAll('.quantity-btn.plus');

// // Decrease quantity
// minusBtns.forEach(btn => {
//     btn.addEventListener('click', function() {
//         const input = this.nextElementSibling;
//         const value = parseInt(input.value);
//         if (value > 1) {
//             input.value = value - 1;
//         }
//     });
// });

// // Increase quantity
// plusBtns.forEach(btn => {
//     btn.addEventListener('click', function() {
//         const input = this.previousElementSibling;
//         const value = parseInt(input.value);
//         if (value < 99) {
//             input.value = value + 1;
//         }
//     });
// });


// overlay click event
overlay.addEventListener('click', () => {
    const cartMenu = document.getElementById('cart-menu');

    // Close mobile menu if it's open
    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
    }

    // Close cart if it's open
    if (cartMenu.classList.contains('active')) {
        cartMenu.classList.remove('slide-in');
        cartMenu.classList.add('slide-out'); 
        setTimeout(() => {
            cartMenu.classList.remove('active'); 
        }, 300);
    }

    // Hide overlay and restore scrolling
    overlay.classList.remove('active');
    overlay.style.display = 'none';
    document.body.style.overflow = 'auto';
});

// Get all slides and pagination dots
const heroSlides = document.querySelectorAll('.hero-container .hero-slide');
const heroPagination = document.querySelectorAll('.hero-container .dot');

// Add click event listeners to dots
heroPagination.forEach(dot => {
    dot.addEventListener('click', () => {
        // Get the slide index from the data attribute
        const slideIndex = parseInt(dot.getAttribute('data-slide'));
        
        // Remove active class from all heroSlides and dots
        heroSlides.forEach(slide => slide.classList.remove('active'));
        heroPagination.forEach(dot => dot.classList.remove('active'));
        
        // Add active class to the selected slide and dot
        heroSlides[slideIndex].classList.add('active');
        dot.classList.add('active');
    });
});

// close popup
const closeButton = document.querySelector('.close-button');
const popupWrapper = document.querySelector('.popup-wrapper');

if (popupWrapper){
    // Function to show the popup after 2 minutes
    setTimeout(() => {
        popupWrapper.classList.add('active'); 
        document.body.style.overflow = 'hidden'; 
    }, 5000); 

    // Close the popup manually
    closeButton.addEventListener('click', () => {
        popupWrapper.classList.remove('active'); 
        document.body.style.overflow = 'auto'; 
    });
}

// Toggle FAQ items
const faqItems = document.querySelectorAll('.faq-item');

faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    
    question.addEventListener('click', () => {
        // Close all other items
        faqItems.forEach(otherItem => {
            if (otherItem !== item && otherItem.classList.contains('active')) {
                otherItem.classList.remove('active');
            }
        });
        
        // Toggle current item
        item.classList.toggle('active');
    });
});

// Back to top button functionality
const backToTopButton = document.querySelector('.back-to-top');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopButton.classList.add('visible');
    } else {
        backToTopButton.classList.remove('visible');
    }
});

backToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});