class CartManager {
    constructor() {
        this.isLoading = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCartData();
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.add-to-cart-btn');
            if (!button) return;

            e.preventDefault();
            const isInCart = button.dataset.inCart === 'true';
            
            if (isInCart) {
                this.removeFromCart(button.dataset.cartItemId);
            } else {
                this.handleAddToCart(button);
            }
        });
    }

    async handleAddToCart(button) {
        if (this.isLoading) return;

        this.setButtonLoading(button, true);
        this.isLoading = true;

        const productId = button.dataset.productId;
        const quantity = parseInt(button.dataset.quantity) || 1;

        try {
            const response = await this.apiCall('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: button.dataset.productId,
                    quantity: parseInt(button.dataset.quantity) || 1,
                })
            });

            if (response.success) {
                // Update cart icon and sidebar
                this.updateCartUI(response.cart);

                // If cart menu is open, refresh its HTML
                if (this.isCartMenuOpen()) {
                    await this.refreshCartMenu();
                }

                // Find the cart item for this product
                const cartItem = response.cart.items.find(item => item.product_id == button.dataset.productId);
                if (cartItem) {
                    button.dataset.cartItemId = cartItem.id; // <-- Set the cart item ID here!
                }

                // Change button text to "Remove from Cart"
                this.updateButtonState(button, true);

                this.showNotification('Item added to cart', 'success');
            } else {
                this.showNotification(response.message || 'Failed to add item to cart', 'error');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showNotification('Failed to add item to cart', 'error');
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async updateQuantity(cartItemId, quantity) {
        if (this.isLoading) return;

        this.isLoading = true;

        try {
            const response = await this.apiCall('/cart/update', {
                method: 'PATCH',
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: quantity
                })
            });

            if (response.success) {
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to update cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async removeFromCart(cartItemId) {
        console.log('Trying to remove cart item:', cartItemId); // Debug
        // if (this.isLoading) return;

        // this.isLoading = true;

        // try {
        //     const response = await this.apiCall('/cart/remove', {
        //         method: 'DELETE',
        //         body: JSON.stringify({
        //             cart_item_id: cartItemId
        //         })
        //     });

        //     if (response.success) {
        //         // if (response.cart) {
        //         //     this.updateCartUI(response.cart);
        //         // } else {
        //         //     this.updateCartUI({
        //         //         count: response.cartCount,
        //         //         total: response.cartTotal
        //         //     });
        //         // }
                
        //         // // Update all buttons for this product
        //         // const productId = response.product_id;
        //         // if (productId) {
        //         //     const buttons = document.querySelectorAll(`.add-to-cart-btn[data-product-id="${productId}"]`);
        //         //     buttons.forEach(button => {
        //         //         this.updateButtonState(button, false);
        //         //         button.dataset.inCart = "false";
        //         //         button.dataset.cartItemId = "";
        //         //     });
        //         // }

        //         this.updateCartUI(response.cart);
        //         const productId = response.product_id;
        //         if (productId) {
        //             const buttons = document.querySelectorAll(`.add-to-cart-btn[data-product-id="${productId}"]`);
        //             buttons.forEach(button => {
        //                 this.updateButtonState(button, false);
        //                 button.dataset.inCart = "false";
        //                 button.dataset.cartItemId = "";
        //             });
        //         }

        //         this.showNotification('Item removed from cart', 'success');
        //     } else {
        //         this.showNotification(response.message, 'error');
        //     }
        // } catch (error) {
        //     this.showNotification('Failed to remove item', 'error');
        // } finally {
        //     this.isLoading = false;
        // }
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
                this.updateCartUI(response.cart);
                this.showNotification(response.message, 'success');
            } else {
                this.showNotification(response.message, 'error');
            }
        } catch (error) {
            this.showNotification('Failed to clear cart', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async loadCartData() {

        try {
            const response = await this.apiCall('/cart/summary');
            if (response.success) {
                this.updateCartUI(response.cart);
            }
        } catch (error) {
            console.error('Failed to load cart data:', error);
        }
    }

    updateCartUI(cartData) {
        // Update cart count
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = cartData.count || '0';
            element.style.display = cartData.count > 0 ? 'flex' : 'none';
        });

        // Update cart total if exists
        const cartTotalElements = document.querySelectorAll('.cart-total');
        cartTotalElements.forEach(element => {
            element.textContent = this.formatPrice(cartData.total || 0);
        });
    
        // Refresh cart menu if open
        if (this.isCartMenuOpen()) {
            this.refreshCartMenu();
        }
    }

    updateButtonState(button, inCart) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');

        if (btnText) {
            btnText.textContent = inCart ? 'Remove from Cart' : 'Add to Cart';
        }

        // button.classList.toggle('in-cart', inCart);
        // button.onclick = inCart ? 
        //     () => this.removeFromCart(button.dataset.productId) : 
        //     () => this.handleAddToCart(button);
        button.classList.toggle('in-cart', inCart);
        button.dataset.inCart = inCart.toString();
    }

    toggleCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const overlay = document.getElementById('overlay');

        if (!cartMenu || !overlay) return; 

        const isMenuOpen = cartMenu.classList.contains('active');
        

        if (isMenuOpen) {
            this.closeCartMenu();
        } else {
            this.openCartMenu();
        }
    }

    openCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const overlay = document.getElementById('overlay');
        
        overlay.style.display = 'block';

        if (cartMenu) {
            cartMenu.classList.add('active', 'slide-in');
            cartMenu.classList.remove('slide-out');
            document.body.style.overflow = 'hidden';
            console.log('clicked');
        }   
    }

    closeCartMenu() {
        const cartMenu = document.getElementById('cart-menu');
        const overlay = document.getElementById('overlay');
    
        if (!cartMenu || !overlay) return; // Guard clause for missing elements
    
        // Add the slide-out animation
        cartMenu.classList.remove('slide-in');
        cartMenu.classList.add('slide-out');
    
        // Remove active class from overlay
        overlay.classList.remove('active');
        overlay.style.display = 'none';
    
        // Reset body scroll
        document.body.style.overflow = 'auto';
    
        // Remove active class from cart menu after animation
        setTimeout(() => {
            cartMenu.classList.remove('active', 'slide-out');
        }, 300); // Match this with your CSS animation duration
    }

    isCartMenuOpen() {
        // const cartMenu = document.getElementById('cart-menu');
        // return cartMenu.style.display === 'flex';

        const cartMenu = document.getElementById('cart-menu');
        return cartMenu && cartMenu.classList.contains('active');
    }

    async refreshCartMenu() {
        // try {
        //     const response = await fetch('/cart/summary');
        //     const data = await response.json();
            
        //     if (data.success) {
        //         // Reload the cart menu component
        //         location.reload(); // Simple approach, or implement dynamic HTML update
        //     }
        // } catch (error) {
        //     console.error('Failed to refresh cart menu:', error);
        // }

        try {
            const response = await this.apiCall('/cart/summary');
            if (response.success) {
                const cartMenu = document.getElementById('cart-menu');
                if (cartMenu) {
                    cartMenu.innerHTML = response.html;
                }
            }
        } catch (error) {
            console.error('Failed to refresh cart menu:', error);
        }
    }

    setButtonLoading(button, loading) {
        // const text = button.querySelector('.btn-text');
        // const loader = button.querySelector('.btn-loading');
        
        // if (loading) {
        //     text.style.display = 'none';
        //     loader.style.display = 'inline-block';
        //     button.disabled = true;
        // } else {
        //     text.style.display = 'inline-block';
        //     loader.style.display = 'none';
        //     button.disabled = false;
        // }

        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnLoading = button.querySelector('.btn-loading');

        if (btnText && btnLoading) {
            btnText.style.display = loading ? 'none' : 'block';
            btnLoading.style.display = loading ? 'block' : 'none';
        }

        button.disabled = loading;
    }

    async apiCall(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };

        const response = await fetch(url, { ...defaultOptions, ...options });           
        const data = await response.json();

        
        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }
        
        return data;
    }

    showNotification(message, type = 'info') {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: type === 'success' ? 'Success' : 'Error',
                message: message,
                timeout: 3000
            });
        } else {
            alert(message); // Fallback
        }
    }

    formatPrice(price) {
        // return new Intl.NumberFormat('en-US', {
        //     minimumFractionDigits: 2,
        //     maximumFractionDigits: 2
        // }).format(price);
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    }
}

// Global functions for inline event handlers
window.updateQuantity = (cartItemId, quantity) => CartManager.updateQuantity(cartItemId, quantity);
window.removeFromCart = (cartItemId) => CartManager.removeFromCart(cartItemId);
window.clearCart = () => CartManager.clearCart();
window.toggleCartMenu = () => window.CartManager.toggleCartMenu();

// Initialize cart manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.CartManager = new CartManager();
});