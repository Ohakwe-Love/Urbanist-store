document.addEventListener('DOMContentLoaded', function() {
    function updateWishlistCount(count) {
        const wishlistCounter = document.querySelector('.wishlist-count');
        if (wishlistCounter) {
            wishlistCounter.textContent = count;
        }
    }

    function handleWishlistClick(button) {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            // Check if user is logged in
            const isLoggedIn = this.dataset.auth === '1';
            if (!isLoggedIn) {
                const currentPath = encodeURIComponent(window.location.pathname);
                window.location.href = `/login?redirect=${currentPath}`;
                return;
            }

            const productId = this.getAttribute('data-product-id') || this.dataset.productId;
            const token = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(`/user/wishlist/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                const icon = this.querySelector('i');

                // Update wishlist count based on response
                if (data.count !== undefined) {
                    updateWishlistCount(data.count);
                }
                
                if (data.status === 'added') {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid', 'text-red-500');
                    iziToast.success({
                        title: 'Success',
                        message: data.message,
                    });
                } else if (data.status === 'removed') {
                    icon.classList.remove('fa-solid', 'text-red-500');
                    icon.classList.add('fa-regular');
                    iziToast.success({
                        title: 'Success',
                        message: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'Something went wrong',
                });
            }
        });
    }

    // Handle initial buttons
    document.querySelectorAll('.wishlist-toggle-btn').forEach(button => {
        handleWishlistClick(button);
    });

    // Set up a MutationObserver to watch for new buttons
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1 && node.matches('.wishlist-toggle-btn')) {
                    handleWishlistClick(node);
                }
                if (node.nodeType === 1) {
                    node.querySelectorAll('.wishlist-toggle-btn').forEach(button => {
                        handleWishlistClick(button);
                    });
                }
            });
        });
    });

    // Start observing the document with the configured parameters
    observer.observe(document.body, { childList: true, subtree: true });

    const removeButtons = document.querySelectorAll('.remove-from-wishlist-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            const wishlistItem = this.closest('.wishlist-item');
            const token = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(`/user/wishlist/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.status === 'removed') {
                    // Remove the wishlist item with animation
                    wishlistItem.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        wishlistItem.remove();
                        // Check if there are no more items
                        const remainingItems = document.querySelectorAll('.wishlist-item');
                        if (remainingItems.length === 0) {
                            const emptyWishlistContainer = document.querySelector('.no-wishlists-container');
                            emptyWishlistContainer.style.display = 'flex';
                        }
                    }, 300);

                    // Update the wishlist count in header
                    if (data.count !== undefined) {
                        updateWishlistCount(data.count);
                    }

                    iziToast.success({
                        title: 'Success',
                        message: data.message,
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'Something went wrong',
                });
            }
        });
    });
});