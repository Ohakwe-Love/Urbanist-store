class CartManager {
    constructor() {
        this.isLoading = false;
        this.cartData = { items: [], total: 0, count: 0, product_ids: [] };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCartData();
    }

    bindEvents() {
        // Add to cart / Remove from cart button handler
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.add-to-cart-btn');
            if (!button) return;

            e.preventDefault();
            const isInCart = button.dataset.inCart === 'true';

            if (isInCart) {
                this.removeFromCart(button.dataset.cartItemId, button);
            } else {
                this.handleAddToCart(button);
            }
        });

        // Cart toggle button
        const cartToggle = document.getElementById('cart-toggle');
        if (cartToggle) {
            cartToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCartMenu();
            });
        }

        // Close cart button
        const closeCart = document.getElementById('close-cart');
        if (closeCart) {
            closeCart.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeCartMenu();
            });
        }

        // Overlay click to close
        const overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.addEventListener('click', () => this.closeCartMenu());
        }

        // Clear cart button
        document.addEventListener('click', (e) => {
            if (e.target.id === 'clear-cart' || e.target.closest('#clear-cart')) {
                e.preventDefault();
                this.clearCart();
            }
        });

        // Quantity update buttons in cart menu
        document.addEventListener('click', (e) => {
            const qtyBtn = e.target.closest('[data-action="update-qty"]');
            if (qtyBtn) {
                e.preventDefault();
                const cartItemId = qtyBtn.dataset.cartItemId;
                const newQty = parseInt(qtyBtn.dataset.quantity);
                this.updateQuantity(cartItemId, newQty, qtyBtn);
            }
        });

        // Remove item buttons in cart menu
        document.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('[data-action="remove-item"]');
            if (removeBtn) {
                e.preventDefault();
                const cartItemId = removeBtn.dataset.cartItemId;
                this.removeFromCart(cartItemId);
            }
        });
    }

    async handleAddToCart(button) {
        if (this.isLoading) return;

        // Check stock before attempting to add
        const stock = parseInt(button.dataset.stock || 0);
        const quantity = parseInt(button.dataset.quantity) || 1;

        if (stock < 1) {
            this.showNotification('Sorry, this product is out of stock', 'warning');
            return;
        }

        if (stock < quantity) {
            this.showNotification(`Only ${stock} items available in stock`, 'warning');
            return;
        }

        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: button.dataset.productId,
                    quantity: quantity,
                })
            });

            if (response.success) {
                this.cartData = response.cart;
                this.updateCartUI(response.cart);

                // Find the cart item for this product and store its ID
                const cartItem = response.cart.items.find(item => item.product_id == button.dataset.productId);
                if (cartItem) {
                    button.dataset.cartItemId = cartItem.id;
                }

                // Update button state
                this.updateButtonState(button, true);

                // Update all other buttons for this product
                this.syncButtonStates();

                // Refresh cart menu content
                await this.refreshCartMenu();

                this.showNotification(response.message || 'Item added to cart', 'success');
            } else {
                this.showNotification(response.message || 'Failed to add item to cart', 'error');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showNotification(error.message || 'Failed to add item to cart', 'error');
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async updateQuantity(cartItemId, quantity, button = null) {
        if (this.isLoading) return;

        // Don't allow quantity less than 0
        if (quantity < 0) return;

        // If quantity is 0, remove the item
        if (quantity === 0) {
            this.removeFromCart(cartItemId);
            return;
        }

        // Get stock from button data attribute or find it in cart data
        let stock = 0;
        if (button && button.dataset.stock) {
            stock = parseInt(button.dataset.stock);
        } else {
            // Fallback: find the item in cart data
            const cartItem = this.cartData.items?.find(item => item.id == cartItemId);
            if (cartItem) {
                stock = cartItem.stock || 0;
            }
        }

        // Validate stock
        if (stock < 1) {
            this.showNotification('Sorry, this product is out of stock', 'warning');
            return;
        }

        if (stock < quantity) {
            this.showNotification(`Only ${stock} items available in stock`, 'warning');
            return;
        }

        this.isLoading = true;

        console.log(stock);


        try {
            const response = await this.apiCall('/cart/update', {
                method: 'PATCH',
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: quantity
                })
            });

            if (response.success) {
                this.cartData = response.cart;
                this.updateCartUI(response.cart);

                // Refresh cart menu to show updated quantities
                await this.refreshCartMenu();

                this.showNotification(response.message || 'Cart updated', 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Update quantity error:', error);
            this.showNotification(error.message || 'Failed to update cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async removeFromCart(cartItemId, button = null) {
        if (this.isLoading) return;

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/remove', {
                method: 'DELETE',
                body: JSON.stringify({
                    cart_item_id: cartItemId
                })
            });

            if (response.success) {
                this.cartData = response.cart;
                this.updateCartUI(response.cart);

                // If button was passed, update its state
                if (button) {
                    this.updateButtonState(button, false);
                    button.removeAttribute('data-cart-item-id');
                }

                // Refresh cart menu if open
                if (this.isCartMenuOpen()) {
                    await this.refreshCartMenu();
                }

                // Update all buttons for removed product
                this.syncButtonStates();

                this.showNotification(response.message || 'Item removed from cart', 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Remove from cart error:', error);
            this.showNotification('Failed to remove item', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async clearCart() {
        if (this.isLoading) return;

        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/clear', {
                method: 'DELETE'
            });

            if (response.success) {
                this.cartData = response.cart;
                this.updateCartUI(response.cart);

                // Refresh cart menu to show empty state
                if (this.isCartMenuOpen()) {
                    await this.refreshCartMenu();
                }

                // Reset all add to cart buttons
                this.syncButtonStates();

                this.showNotification(response.message || 'Cart cleared', 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            console.error('Clear cart error:', error);
            this.showNotification('Failed to clear cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async loadCartData() {
        try {
            const response = await this.apiCall('/cart/summary');
            if (response.success) {
                this.cartData = response.cart;
                this.updateCartUI(response.cart);
                this.syncButtonStates();
            }
        } catch (error) {
            console.error('Failed to load cart data:', error);
        }
    }

    updateCartUI(cartData) {
        // Update cart count badges
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = cartData.count || '0';
            element.style.display = cartData.count > 0 ? 'flex' : 'none';
        });

        // Update cart total displays
        const cartTotalElements = document.querySelectorAll('.cart-total');
        cartTotalElements.forEach(element => {
            element.textContent = this.formatPrice(cartData.total || 0);
        });

        // Update empty/content state in cart menu
        const emptyCartMessage = document.getElementById('empty-cart');
        const cartContent = document.getElementById('cart-content');

        if (emptyCartMessage && cartContent) {
            if (cartData.count === 0) {
                emptyCartMessage.style.display = 'block';
                cartContent.style.display = 'none';
            } else {
                emptyCartMessage.style.display = 'none';
                cartContent.style.display = 'block';
            }
        }
    }

    syncButtonStates() {
        // Find all add-to-cart buttons and update their states
        const buttons = document.querySelectorAll('[data-product-id]');

        buttons.forEach(button => {
            const productId = parseInt(button.dataset.productId);
            const cartItem = this.cartData.items?.find(item => item.product_id == productId);

            if (cartItem) {
                // Product is in cart
                button.dataset.cartItemId = cartItem.id;
                this.updateButtonState(button, true);
            } else {
                // Product is not in cart
                button.removeAttribute('data-cart-item-id');
                this.updateButtonState(button, false);
            }
        });
    }

    updateButtonState(button, inCart) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');

        if (btnText) {
            const addText = button.dataset.addText || 'Add to Cart';
            const removeText = button.dataset.removeText || 'Remove from Cart';
            btnText.textContent = inCart ? removeText : addText;
        }

        button.classList.toggle('in-cart', inCart);
        button.dataset.inCart = inCart.toString();
    }

    toggleCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        if (!cartMenu) return;

        const isOpen = cartMenu.classList.contains('active');

        if (isOpen) {
            this.closeCartMenu();
        } else {
            this.openCartMenu();
        }
    }

    openCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const overlay = document.getElementById('overlay');

        if (!cartMenu) return;

        cartMenu.classList.add('active', 'slide-in');
        cartMenu.classList.remove('slide-out');

        if (overlay) {
            overlay.style.display = 'block';
            overlay.classList.add('active');
        }

        document.body.style.overflow = 'hidden';
    }

    closeCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const overlay = document.getElementById('overlay');

        if (!cartMenu) return;

        cartMenu.classList.remove('slide-in');
        cartMenu.classList.add('slide-out');

        if (overlay) {
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        }

        document.body.style.overflow = 'auto';

        setTimeout(() => {
            cartMenu.classList.remove('active', 'slide-out');
        }, 300);
    }

    isCartMenuOpen() {
        const cartMenu = document.getElementById('cart-menu');
        return cartMenu && cartMenu.classList.contains('active');
    }

    async refreshCartMenu() {
        try {
            const response = await this.apiCall('/cart/summary');
            if (response.success && response.html) {
                const cartMenuContent = document.getElementById('cart-menu-content');
                const cartMenuCount = document.getElementById('cart-menu-count');

                if (cartMenuContent) {
                    cartMenuContent.innerHTML = response.html;
                }

                if (cartMenuCount) {
                    cartMenuCount.textContent = response.cart.count;
                }
            }
        } catch (error) {
            console.error('Failed to refresh cart menu:', error);
        }
    }

    setButtonLoading(button, loading) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');

        if (btnText) {
            btnText.style.display = loading ? 'none' : 'block';
        }

        if (btnLoading) {
            btnLoading.style.display = loading ? 'block' : 'none';
        }

        button.disabled = loading;
        button.style.opacity = loading ? '0.7' : '1';
        button.style.cursor = loading ? 'not-allowed' : 'pointer';
    }

    async apiCall(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
            }
        };

        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        } catch (error) {
            console.error('API call error:', error);
            throw error;
        }
    }

    showNotification(message, type = 'info') {
        // Check for iziToast library
        if (typeof iziToast !== 'undefined') {
            const options = {
                message: message,
                timeout: 3000,
                position: 'topRight',
                transitionIn: 'fadeInDown',
                transitionOut: 'fadeOutUp'
            };

            switch (type) {
                case 'success':
                    iziToast.success({ title: 'Success', ...options });
                    break;
                case 'error':
                    iziToast.error({ title: 'Error', ...options });
                    break;
                case 'warning':
                    iziToast.warning({ title: 'Warning', ...options });
                    break;
                default:
                    iziToast.info({ title: 'Info', ...options });
            }
        } else {
            // Fallback to browser alert
            console.log(`[${type.toUpperCase()}] ${message}`);
            alert(message);
        }
    }

    formatPrice(price) {
        return new Intl.NumberFormat('en-NG', {
            style: 'currency',
            currency: 'NGN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price);
    }

    // Utility method to get cart data
    getCartData() {
        return this.cartData;
    }

    // Utility method to check if product is in cart
    isProductInCart(productId) {
        return this.cartData.items?.some(item => item.product_id == productId) || false;
    }

    // Utility method to get cart item by product ID
    getCartItemByProductId(productId) {
        return this.cartData.items?.find(item => item.product_id == productId) || null;
    }

    syncButtonStates() {
        const buttons = document.querySelectorAll('.add-to-cart-btn');

        buttons.forEach(button => {
            const productId = parseInt(button.dataset.productId);
            const cartItem = this.cartData.items?.find(item => item.product_id == productId);

            if (cartItem) {
                // Product is in cart
                button.dataset.cartItemId = cartItem.id;
                this.updateButtonState(button, true);
            } else {
                // Product is not in cart
                button.dataset.cartItemId = '';
                this.updateButtonState(button, false);
            }
        });
    }
}

// Initialize cart manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.cartManager = new CartManager();
    });
} else {
    window.cartManager = new CartManager();
}