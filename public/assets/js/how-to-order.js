// Accordion functionality
document.querySelectorAll('.accordion-header').forEach(button => {
    button.addEventListener('click', () => {
        const content = button.nextElementSibling;
        
        button.classList.toggle('active');
        content.classList.toggle('active');
    });
});

// Video placeholder click functionality
document.querySelector('.play-button').addEventListener('click', function() {
    alert('Video would play here. This is a placeholder for your actual video tutorial.');
});

// Animation on scroll (simple implementation)
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-fade-in-up');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
    });
    
    function checkPosition() {
        let windowHeight = window.innerHeight;
        
        animatedElements.forEach(el => {
            let positionFromTop = el.getBoundingClientRect().top;
            
            if (positionFromTop - windowHeight <= -100) {
                el.style.opacity = '1';
            }
        });
    }
    
    window.addEventListener('scroll', checkPosition);
    checkPosition();
});