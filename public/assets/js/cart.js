const CartUI = {
    cartToggle: document.querySelector('.cart-btn'),
    cartMenu: document.getElementById('cart-menu'),
    cartClose: document.getElementById('cart-menu-close'),
    overlay: document.querySelector('.overlay'),
    navCartCount: document.getElementById('nav-cart-count'),

    init() {
        this.bindEvents();
    },

    bindEvents() {
        // Cart menu toggle
        this.cartToggle?.addEventListener('click', () => this.openCart());
        this.cartClose?.addEventListener('click', () => this.closeCart());
        this.overlay?.addEventListener('click', () => this.closeCart());

        
        // Add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn')) {
                this.handleAddToCartClick(e);
            }
        });

        // Cart quantity buttons
        this.cartMenu?.addEventListener('click', (e) => {
            if (e.target.matches('.quantity-btn')) {
                this.handleQuantityButton(e);
            }
        });

        // Add escape key handler
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.cartMenu.classList.contains('active')) {
                this.closeCart();
            }
        });
    },

    openCart() {
        this.cartMenu.classList.add('active', 'slide-in');
        this.cartMenu.classList.remove('slide-out');
        this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    closeCart() {
        this.cartMenu.classList.remove('slide-in');
        this.cartMenu.classList.add('slide-out');
        setTimeout(() => {
            this.cartMenu.classList.remove('active');
        }, 300);
        this.overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    },

    async handleAddToCartClick(event) {
        const button = event.target;
        const productId = button.closest('.add-to-cart-container').dataset.productId;
        const isInCart = button.classList.contains('in-cart');

        button.disabled = true;
        const originalText = button.textContent;
        button.textContent = isInCart ? 'Removing...' : 'Adding...';

        try {
            if (isInCart) {
                await this.removeFromCart(productId, button);
            } else {
                await this.addToCart(productId, button);
            }
        } catch (error) {
            console.error('Error:', error);
            button.textContent = originalText;
            button.disabled = false;
            iziToast.error({
                title: 'Error',
                message: 'Something went wrong. Please try again.',
            });
        }
    },

    // async addToCart(productId, button) {
    //     try {
    //         const response = await fetch(`/user/cart/add/${productId}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'Accept': 'application/json',  // Add this line
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             },
    //             body: JSON.stringify({ quantity: 1 })
    //         });
    
    //         if (!response.ok) {
    //             throw new Error(`HTTP error! status: ${response.status}`);
    //         }
    
    //         const data = await response.json();
            
    //         if (data.success) {
    //             button.textContent = 'Remove Item';
    //             button.classList.add('in-cart');
    //             this.updateCartCount(data.cartCount);
    //             this.refreshCartMenu();
                
    //             iziToast.success({
    //                 title: 'Success',
    //                 message: 'Item added to cart',
    //             });
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         throw error;
    //     } finally {
    //         button.disabled = false;
    //     }
    // },

    // async addToCart(productId, button) {
    //     try {
    //         const response = await fetch(`/user/cart/add/${productId}`, {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'Accept': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             },
    //             body: JSON.stringify({ quantity: 1 })
    //         });
    
    //         if (!response.ok) {
    //             throw new Error(`HTTP error! status: ${response.status}`);
    //         }
    
    //         const data = await response.json();
            
    //         if (data.success) {
    //             button.textContent = 'Remove Item';
    //             button.classList.add('in-cart');
    //             this.updateCartCount(data.cartCount);
    //             await this.refreshCartMenu(); // Added await here
                
    //             iziToast.success({
    //                 title: 'Success',
    //                 message: 'Item added to cart',
    //                 position: 'topRight'
    //             });
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         throw error;
    //     } finally {
    //         button.disabled = false;
    //     }
    // },
   
    async addToCart(productId, button) {
        try {
            const response = await fetch(`/user/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity: 1 })
            });

            const data = await response.json();
            
            if (data.success) {
                button.textContent = 'Remove Item';
                button.classList.add('in-cart');
                await this.refreshCartContents(data);
                
                iziToast.success({
                    title: 'Success',
                    message: 'Item added to cart',
                    position: 'topRight'
                });
            } else {
                throw new Error(data.message || 'Failed to add item to cart');
            }
        } catch (error) {
            console.error('Error:', error);
            throw error;
        } finally {
            button.disabled = false;
        }
    },
    
    // async removeFromCart(productId, button) {
    //     try {
    //         const response = await fetch(`/user/cart/remove/${productId}`, {
    //             method: 'DELETE',
    //             headers: {
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    //                 'Accept': 'application/json'
    //             }
    //         });

    //         if (!response.ok) {
    //             throw new Error(`HTTP error! status: ${response.status}`);
    //         }

    //         const data = await response.json();
            
    //         if (data.success) {
    //             button.textContent = 'Add to Cart';
    //             button.classList.remove('in-cart');
    //             this.updateCartCount(data.cartCount);
    //             await this.refreshCartMenu();
                
                
    //             iziToast.info({
    //                 title: 'Info',
    //                 message: 'Item removed from cart',
    //             });
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         throw error;
    //     } finally {
    //         button.disabled = false;
    //     }
    // },

    async removeFromCart(productId, button) {
        try {
            const response = await fetch(`/user/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                button.textContent = 'Add to Cart';
                button.classList.remove('in-cart');
                await this.refreshCartContents(data);
                
                iziToast.info({
                    title: 'Info',
                    message: 'Item removed from cart',
                    position: 'topRight'
                });
            } else {
                throw new Error(data.message || 'Failed to remove item from cart');
            }
        } catch (error) {
            console.error('Error:', error);
            throw error;
        } finally {
            button.disabled = false;
        }
    },

    async handleQuantityButton(event) {
        const button = event.target;
        const action = button.dataset.action;
        const productId = button.dataset.productId;
        const input = this.cartMenu.querySelector(`input[data-product-id="${productId}"]`);
        let quantity = parseInt(input.value);

        if (action === 'increase') {
            quantity++;
        } else if (action === 'decrease' && quantity > 1) {
            quantity--;
        }

        input.value = quantity;
        await this.updateCartItem(productId, quantity);
    },

    // async updateCartItem(productId, quantity) {
    //     try {
    //         const response = await fetch(`/user/cart/update/${productId}`, {
    //             method: 'PATCH',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             },
    //             body: JSON.stringify({ quantity })
    //         });

    //         const data = await response.json();
            
    //         if (data.success) {
    //             this.updateCartCount(data.cartCount);
    //             this.refreshCartMenu();
                
    //             iziToast.success({
    //                 title: 'Success',
    //                 message: 'Cart updated',

    //             });
    //         }
    //     } catch (error) {
    //         console.error('Error:', error);
    //         iziToast.error({
    //             title: 'Error',
    //             message: 'Failed to update cart',
    //         });
    //     }
    // },

    // updateCartCount(count) {
    //     if (this.navCartCount) {
    //         this.navCartCount.textContent = count;
    //     }
    // },

    // async refreshCartMenu() {
    //     try {
    //         const response = await fetch('/user/cart/menu', {
    //             headers: {
    //                 'X-Requested-With': 'XMLHttpRequest',
    //                 'Accept': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             }
    //         });

    //         if (!response.ok) {
    //             throw new Error(`HTTP error! status: ${response.status}`);
    //         }

    //         const data = await response.json();
            
    //         if (data.success) {
    //             // Update cart count
    //             this.updateCartCount(data.cartCount);
                
    //             // Update cart items if cart menu exists
    //             if (this.cartMenu) {
    //                 const cartItemsContainer = this.cartMenu.querySelector('.cart-items');
    //                 if (cartItemsContainer && data.cartItems) {
    //                     cartItemsContainer.innerHTML = this.generateCartItemsHtml(data.cartItems);
    //                 }
                    
    //                 // Update cart total
    //                 const cartTotalElement = this.cartMenu.querySelector('.cart-total-amount');
    //                 if (cartTotalElement && data.cartTotal) {
    //                     cartTotalElement.textContent = `$${parseFloat(data.cartTotal).toFixed(2)}`;
    //                 }
    //             }
    //         } else {
    //             throw new Error(data.message || 'Failed to refresh cart');
    //         }
    //     } catch (error) {
    //         console.error('Error refreshing cart:', error);
    //         iziToast.error({
    //             title: 'Error',
    //             message: 'Failed to refresh cart',
    //             position: 'topRight'
    //         });
    //     }
    // },

    async updateCartItem(productId, quantity) {
        try {
            const response = await fetch(`/user/cart/update/${productId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity })
            });

            const data = await response.json();
            
            if (data.success) {
                await this.refreshCartContents(data);
                
                iziToast.success({
                    title: 'Success',
                    message: 'Cart updated',
                    position: 'topRight'
                });
            } else {
                throw new Error(data.message || 'Failed to update cart');
            }
        } catch (error) {
            console.error('Error:', error);
            iziToast.error({
                title: 'Error',
                message: 'Failed to update cart',
                position: 'topRight'
            });
        }
    },
    
    // async refreshCartMenu() {
    //     try {
    //         const response = await fetch('/user/cart/menu', {
    //             headers: {
    //                 'X-Requested-With': 'XMLHttpRequest',
    //                 'Accept': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //             }
    //         });

    //         if (!response.ok) {
    //             throw new Error(`HTTP error! status: ${response.status}`);
    //         }

    //         const data = await response.json();
            
    //         if (data.success) {
    //             // Update cart count
    //             this.updateCartCount(data.cartCount);
                
    //             // Update cart menu content if it exists
    //             if (this.cartMenu) {
    //                 // Update the entire cart menu content with the new HTML
    //                 const cartContent = this.cartMenu.querySelector('.cart-content');
    //                 if (cartContent && data.html) {
    //                     cartContent.innerHTML = data.html;
    //                 }
    //             }
    //         } else {
    //             throw new Error(data.message || 'Failed to refresh cart');
    //         }
    //     } catch (error) {
    //         console.error('Error refreshing cart:', error);
    //         iziToast.error({
    //             title: 'Error',
    //             message: error.message || 'Failed to refresh cart',
    //             position: 'topRight'
    //         });
    //     }
    // },

    async refreshCartContents(data) {
        // Update cart count
        this.updateCartCount(data.cartCount);
        
        // Update cart menu content
        if (this.cartMenu) {
            const cartContent = this.cartMenu.querySelector('.cart-content');
            if (cartContent && data.html) {
                cartContent.innerHTML = data.html;
            }

            // Update cart total if it exists
            const cartTotalElement = this.cartMenu.querySelector('.cart-total-amount');
            if (cartTotalElement && data.cartTotal) {
                cartTotalElement.textContent = `$${parseFloat(data.cartTotal).toFixed(2)}`;
            }
        }
    },

    updateCartCount(count) {
        if (this.navCartCount) {
            this.navCartCount.textContent = count || '0';
        }
    }
};

// Initialize cart functionality
document.addEventListener('DOMContentLoaded', () => {
    CartUI.init();
});