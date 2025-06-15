// sidebar display
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main-content');
const openSidebarBtn = document.querySelector('.open-sidebar');
const sidebarOverlay = document.querySelector('.sidebar-overlay');

function closeSidebar() {
    sidebar.classList.remove('open');
    mainContent.classList.remove('full-width');
    sidebarOverlay.style.display = 'none';
}

openSidebarBtn.addEventListener('click', function() {
    if (window.innerWidth <= 900) {
        sidebar.classList.toggle('open');
        mainContent.classList.toggle('full-width');
        // Show or hide overlay
        if (sidebar.classList.contains('open')) {
            sidebarOverlay.style.display = 'block';
        } else {
            sidebarOverlay.style.display = 'none';
        }
    } else {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('full-width');
    }
});

// Close sidebar when overlay is clicked
sidebarOverlay.addEventListener('click', closeSidebar);

// Handle view options
const viewOptions = document.querySelectorAll('.view-option');
const productsRow = document.querySelector('.products-row');

viewOptions.forEach(option => {
    option.addEventListener('click', function() {
        // Remove active class from all options
        viewOptions.forEach(opt => opt.classList.remove('active'));
        // Add active class to clicked option
        this.classList.add('active');
        
        // Get view type
        const viewType = this.getAttribute('data-view');
        
        // Remove all view classes
        productsRow.classList.remove('view-1', 'view-2', 'view-3');
        // Add selected view class
        productsRow.classList.add(`view-${viewType}`);
    });
});

// Handle filter section toggles
const filterTitles = document.querySelectorAll('.filter-title');

filterTitles.forEach(title => {
    title.addEventListener('click', function() {
        const content = this.nextElementSibling;
        const icon = this.querySelector('i');
        
        // Toggle display
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.classList.remove('fa-angle-right');
            icon.classList.add('fa-angle-down');
        } else {
            content.style.display = 'none';
            icon.classList.remove('fa-angle-down');
            icon.classList.add('fa-angle-right');
        }
    });
});


// show skeleton
function showSkeleton() {
    const skeleton = document.getElementById('products-skeleton');
    if (skeleton) skeleton.style.display = 'flex';
}

// hide skeleton
function hideSkeleton() {
    const skeleton = document.getElementById('products-skeleton');
    if (skeleton) skeleton.style.display = 'none';
}

let currentPage = 1;
const loadMoreBtn = document.getElementById('load-more-btn');
const productsContainer = document.getElementById('products-container');

// Handle Load More Button
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
        showSkeleton(); // Show skeleton

        const nextPage = parseInt(this.dataset.page);
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('page', nextPage);

        this.querySelector('.btn-text').style.display = 'none';
        this.querySelector('.btn-loading').style.display = 'inline';
        this.disabled = true;

        fetch(currentUrl.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideSkeleton(); // Hide skeleton
            productsContainer.insertAdjacentHTML('beforeend', data.products);

            if (data.hasMore) {
                this.dataset.page = nextPage + 1;
                this.querySelector('.btn-text').style.display = 'inline';
                this.querySelector('.btn-loading').style.display = 'none';
                this.disabled = false;
            } else {
                this.parentElement.remove();
            }
        })
        .catch(error => {
            hideSkeleton(); // Hide skeleton on error
            this.querySelector('.btn-text').style.display = 'inline';
            this.querySelector('.btn-loading').style.display = 'none';
            this.disabled = false;
        });
    });
}

// Handle Price Filter
const applyPriceBtn = document.getElementById('apply-price-filter');
const minPriceInput = document.getElementById('min-price-input');
const maxPriceInput = document.getElementById('max-price-input');
const priceSlider = document.getElementById('price-slider');
const priceValue = document.querySelector('.price-value');

if (priceSlider) {
    priceSlider.addEventListener('input', function() {
        priceValue.textContent = this.value;
        maxPriceInput.value = this.value;
    });
}

if (applyPriceBtn) {
    applyPriceBtn.addEventListener('click', function() {
        const minPrice = minPriceInput.value || 0;
        const maxPrice = maxPriceInput.value || 10000;

        const url = new URL(window.shopRoute, window.location.origin);
        url.searchParams.set('min_price', minPrice);
        url.searchParams.set('max_price', maxPrice);

        window.location.href = url.toString();
    });
}

// Handle filter clicks to ensure only one filter at a time
// document.querySelectorAll('[data-filter]').forEach(link => {
//     link.addEventListener('click', function(e) {
//         e.preventDefault();
        
//         const filterType = this.dataset.filter;
//         const filterValue = this.dataset.value;
        
//         const url = new URL('{{ route("shop") }}', window.location.origin);
//         url.searchParams.set(filterType, filterValue);
        
//         window.location.href = url.toString();
//     });
// });