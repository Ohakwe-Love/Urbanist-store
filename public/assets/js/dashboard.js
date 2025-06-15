// sidebar display
const sidebar = document.querySelector('.dashboard-sidebar');
const dashboardContent = document.querySelector('.dashboard-main');
const dashboardBtn = document.querySelector('.sidebar-toggle');
const dashboardOverlay = document.querySelector('.dashboard-overlay');

function closeSidebar() {
    sidebar.classList.remove('open');
    dashboardContent.classList.remove('full-width');
    dashboardOverlay.style.display = 'none';
}

dashboardBtn.addEventListener('click', function() {
    if (window.innerWidth <= 900) {
        sidebar.classList.toggle('open');
        dashboardContent.classList.toggle('full-width');
        // Show or hide overlay
        if (sidebar.classList.contains('open')) {
            dashboardOverlay.style.display = 'block';
        } else {
            dashboardOverlay.style.display = 'none';
        }
    } else {
        sidebar.classList.toggle('closed');
        dashboardContent.classList.toggle('full-width');
    }
});

// Close sidebar when overlay is clicked
dashboardOverlay.addEventListener('click', closeSidebar);