// Simple accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            content.classList.toggle('active');
        });
    });

    // Simple tab navigation
    const navItems = document.querySelectorAll('.policy-nav-item');
    const sections = document.querySelectorAll('.policy-section');
    
    navItems.forEach((item, index) => {
        item.addEventListener('click', function() {
            // Hide all sections
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Remove active class from all nav items
            navItems.forEach(navItem => {
                navItem.classList.remove('active');
            });
            
            // Show selected section and add active class to clicked nav item
            sections[index].style.display = 'block';
            this.classList.add('active');
        });
    });

    // Initial setup - show only first section
    for (let i = 1; i < sections.length; i++) {
        sections[i].style.display = 'none';
    }

    // Simple search functionality
    const searchInput = document.querySelector('.search-policies input');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // If empty search, reset to default view
        if (searchTerm === '') {
            navItems[0].click();
            return;
        }
        
        // Show all sections for searching
        sections.forEach(section => {
            section.style.display = 'block';
            
            // Highlight matching text (simple implementation)
            const content = section.innerHTML;
            if (searchTerm.length > 2) { // Only search for terms longer than 2 chars
                if (content.toLowerCase().includes(searchTerm)) {
                    section.scrollIntoView();
                }
            }
        });
        
        // Remove active class from all nav items during search
        navItems.forEach(navItem => {
            navItem.classList.remove('active');
        });
    });
});