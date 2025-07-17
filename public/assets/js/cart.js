// const CartUI = {
//     cartToggle: document.querySelector('.cart-btn'),
//     cartMenu: document.getElementById('cart-menu'),
//     cartClose: document.getElementById('cart-menu-close'),
//     overlay: document.querySelector('.overlay'),
//     navCartCount: document.getElementById('nav-cart-count'),

//     init() {
//         this.bindEvents();
//     },

//     bindEvents() {
//         // Cart menu toggle
//         this.cartToggle?.addEventListener('click', () => this.openCart());
//         this.cartClose?.addEventListener('click', () => this.closeCart());
//         this.overlay?.addEventListener('click', () => this.closeCart());

        
//         // Add to cart buttons
//         document.addEventListener('click', (e) => {
//             if (e.target.matches('.add-to-cart-btn')) {
//                 this.handleAddToCartClick(e);
//             }
//         });

//         // Cart quantity buttons
//         this.cartMenu?.addEventListener('click', (e) => {
//             if (e.target.matches('.quantity-btn')) {
//                 this.handleQuantityButton(e);
//             }
//         });

//         // Add escape key handler
//         document.addEventListener('keydown', (e) => {
//             if (e.key === 'Escape' && this.cartMenu.classList.contains('active')) {
//                 this.closeCart();
//             }
//         });
//     },

//     openCart() {
//         this.cartMenu.classList.add('active', 'slide-in');
//         this.cartMenu.classList.remove('slide-out');
//         this.overlay.classList.add('active');
//         document.body.style.overflow = 'hidden';
//     },

//     closeCart() {
//         this.cartMenu.classList.remove('slide-in');
//         this.cartMenu.classList.add('slide-out');
//         setTimeout(() => {
//             this.cartMenu.classList.remove('active');
//         }, 300);
//         this.overlay.classList.remove('active');
//         document.body.style.overflow = 'auto';
//     },

//     async handleAddToCartClick(event) {
//         const button = event.target;
//         const productId = button.closest('.add-to-cart-container').dataset.productId;
//         const isInCart = button.classList.contains('in-cart');

//         button.disabled = true;
//         const originalText = button.textContent;
//         button.textContent = isInCart ? 'Removing...' : 'Adding...';

//         try {
//             if (isInCart) {
//                 await this.removeFromCart(productId, button);
//             } else {
//                 await this.addToCart(productId, button);
//             }
//         } catch (error) {
//             console.error('Error:', error);
//             button.textContent = originalText;
//             button.disabled = false;
//             iziToast.error({
//                 title: 'Error',
//                 message: 'Something went wrong. Please try again.',
//             });
//         }
//     },

//     // async addToCart(productId, button) {
//     //     try {
//     //         const response = await fetch(`/user/cart/add/${productId}`, {
//     //             method: 'POST',
//     //             headers: {
//     //                 'Content-Type': 'application/json',
//     //                 'Accept': 'application/json',  // Add this line
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//     //             },
//     //             body: JSON.stringify({ quantity: 1 })
//     //         });
    
//     //         if (!response.ok) {
//     //             throw new Error(`HTTP error! status: ${response.status}`);
//     //         }
    
//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             button.textContent = 'Remove Item';
//     //             button.classList.add('in-cart');
//     //             this.updateCartCount(data.cartCount);
//     //             this.refreshCartMenu();
                
//     //             iziToast.success({
//     //                 title: 'Success',
//     //                 message: 'Item added to cart',
//     //             });
//     //         }
//     //     } catch (error) {
//     //         console.error('Error:', error);
//     //         throw error;
//     //     } finally {
//     //         button.disabled = false;
//     //     }
//     // },

//     // async addToCart(productId, button) {
//     //     try {
//     //         const response = await fetch(`/user/cart/add/${productId}`, {
//     //             method: 'POST',
//     //             headers: {
//     //                 'Content-Type': 'application/json',
//     //                 'Accept': 'application/json',
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//     //             },
//     //             body: JSON.stringify({ quantity: 1 })
//     //         });
    
//     //         if (!response.ok) {
//     //             throw new Error(`HTTP error! status: ${response.status}`);
//     //         }
    
//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             button.textContent = 'Remove Item';
//     //             button.classList.add('in-cart');
//     //             this.updateCartCount(data.cartCount);
//     //             await this.refreshCartMenu(); // Added await here
                
//     //             iziToast.success({
//     //                 title: 'Success',
//     //                 message: 'Item added to cart',
//     //                 position: 'topRight'
//     //             });
//     //         }
//     //     } catch (error) {
//     //         console.error('Error:', error);
//     //         throw error;
//     //     } finally {
//     //         button.disabled = false;
//     //     }
//     // },
   
//     async addToCart(productId, button) {
//         try {
//             const response = await fetch(`/user/cart/add/${productId}`, {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'Accept': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//                 },
//                 body: JSON.stringify({ quantity: 1 })
//             });

//             const data = await response.json();
            
//             if (data.success) {
//                 button.textContent = 'Remove Item';
//                 button.classList.add('in-cart');
//                 await this.refreshCartContents(data);
                
//                 iziToast.success({
//                     title: 'Success',
//                     message: 'Item added to cart',
//                     position: 'topRight'
//                 });
//             } else {
//                 throw new Error(data.message || 'Failed to add item to cart');
//             }
//         } catch (error) {
//             console.error('Error:', error);
//             throw error;
//         } finally {
//             button.disabled = false;
//         }
//     },
    
//     // async removeFromCart(productId, button) {
//     //     try {
//     //         const response = await fetch(`/user/cart/remove/${productId}`, {
//     //             method: 'DELETE',
//     //             headers: {
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
//     //                 'Accept': 'application/json'
//     //             }
//     //         });

//     //         if (!response.ok) {
//     //             throw new Error(`HTTP error! status: ${response.status}`);
//     //         }

//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             button.textContent = 'Add to Cart';
//     //             button.classList.remove('in-cart');
//     //             this.updateCartCount(data.cartCount);
//     //             await this.refreshCartMenu();
                
                
//     //             iziToast.info({
//     //                 title: 'Info',
//     //                 message: 'Item removed from cart',
//     //             });
//     //         }
//     //     } catch (error) {
//     //         console.error('Error:', error);
//     //         throw error;
//     //     } finally {
//     //         button.disabled = false;
//     //     }
//     // },

//     async removeFromCart(productId, button) {
//         try {
//             const response = await fetch(`/user/cart/remove/${productId}`, {
//                 method: 'DELETE',
//                 headers: {
//                     'Accept': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//                 }
//             });

//             const data = await response.json();
            
//             if (data.success) {
//                 button.textContent = 'Add to Cart';
//                 button.classList.remove('in-cart');
//                 await this.refreshCartContents(data);
                
//                 iziToast.info({
//                     title: 'Info',
//                     message: 'Item removed from cart',
//                     position: 'topRight'
//                 });
//             } else {
//                 throw new Error(data.message || 'Failed to remove item from cart');
//             }
//         } catch (error) {
//             console.error('Error:', error);
//             throw error;
//         } finally {
//             button.disabled = false;
//         }
//     },

//     async handleQuantityButton(event) {
//         const button = event.target;
//         const action = button.dataset.action;
//         const productId = button.dataset.productId;
//         const input = this.cartMenu.querySelector(`input[data-product-id="${productId}"]`);
//         let quantity = parseInt(input.value);

//         if (action === 'increase') {
//             quantity++;
//         } else if (action === 'decrease' && quantity > 1) {
//             quantity--;
//         }

//         input.value = quantity;
//         await this.updateCartItem(productId, quantity);
//     },

//     // async updateCartItem(productId, quantity) {
//     //     try {
//     //         const response = await fetch(`/user/cart/update/${productId}`, {
//     //             method: 'PATCH',
//     //             headers: {
//     //                 'Content-Type': 'application/json',
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//     //             },
//     //             body: JSON.stringify({ quantity })
//     //         });

//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             this.updateCartCount(data.cartCount);
//     //             this.refreshCartMenu();
                
//     //             iziToast.success({
//     //                 title: 'Success',
//     //                 message: 'Cart updated',

//     //             });
//     //         }
//     //     } catch (error) {
//     //         console.error('Error:', error);
//     //         iziToast.error({
//     //             title: 'Error',
//     //             message: 'Failed to update cart',
//     //         });
//     //     }
//     // },

//     // updateCartCount(count) {
//     //     if (this.navCartCount) {
//     //         this.navCartCount.textContent = count;
//     //     }
//     // },

//     // async refreshCartMenu() {
//     //     try {
//     //         const response = await fetch('/user/cart/menu', {
//     //             headers: {
//     //                 'X-Requested-With': 'XMLHttpRequest',
//     //                 'Accept': 'application/json',
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//     //             }
//     //         });

//     //         if (!response.ok) {
//     //             throw new Error(`HTTP error! status: ${response.status}`);
//     //         }

//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             // Update cart count
//     //             this.updateCartCount(data.cartCount);
                
//     //             // Update cart items if cart menu exists
//     //             if (this.cartMenu) {
//     //                 const cartItemsContainer = this.cartMenu.querySelector('.cart-items');
//     //                 if (cartItemsContainer && data.cartItems) {
//     //                     cartItemsContainer.innerHTML = this.generateCartItemsHtml(data.cartItems);
//     //                 }
                    
//     //                 // Update cart total
//     //                 const cartTotalElement = this.cartMenu.querySelector('.cart-total-amount');
//     //                 if (cartTotalElement && data.cartTotal) {
//     //                     cartTotalElement.textContent = `$${parseFloat(data.cartTotal).toFixed(2)}`;
//     //                 }
//     //             }
//     //         } else {
//     //             throw new Error(data.message || 'Failed to refresh cart');
//     //         }
//     //     } catch (error) {
//     //         console.error('Error refreshing cart:', error);
//     //         iziToast.error({
//     //             title: 'Error',
//     //             message: 'Failed to refresh cart',
//     //             position: 'topRight'
//     //         });
//     //     }
//     // },

//     async updateCartItem(productId, quantity) {
//         try {
//             const response = await fetch(`/user/cart/update/${productId}`, {
//                 method: 'PATCH',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'Accept': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//                 },
//                 body: JSON.stringify({ quantity })
//             });

//             const data = await response.json();
            
//             if (data.success) {
//                 await this.refreshCartContents(data);
                
//                 iziToast.success({
//                     title: 'Success',
//                     message: 'Cart updated',
//                     position: 'topRight'
//                 });
//             } else {
//                 throw new Error(data.message || 'Failed to update cart');
//             }
//         } catch (error) {
//             console.error('Error:', error);
//             iziToast.error({
//                 title: 'Error',
//                 message: 'Failed to update cart',
//                 position: 'topRight'
//             });
//         }
//     },
    
//     // async refreshCartMenu() {
//     //     try {
//     //         const response = await fetch('/user/cart/menu', {
//     //             headers: {
//     //                 'X-Requested-With': 'XMLHttpRequest',
//     //                 'Accept': 'application/json',
//     //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//     //             }
//     //         });

//     //         if (!response.ok) {
//     //             throw new Error(`HTTP error! status: ${response.status}`);
//     //         }

//     //         const data = await response.json();
            
//     //         if (data.success) {
//     //             // Update cart count
//     //             this.updateCartCount(data.cartCount);
                
//     //             // Update cart menu content if it exists
//     //             if (this.cartMenu) {
//     //                 // Update the entire cart menu content with the new HTML
//     //                 const cartContent = this.cartMenu.querySelector('.cart-content');
//     //                 if (cartContent && data.html) {
//     //                     cartContent.innerHTML = data.html;
//     //                 }
//     //             }
//     //         } else {
//     //             throw new Error(data.message || 'Failed to refresh cart');
//     //         }
//     //     } catch (error) {
//     //         console.error('Error refreshing cart:', error);
//     //         iziToast.error({
//     //             title: 'Error',
//     //             message: error.message || 'Failed to refresh cart',
//     //             position: 'topRight'
//     //         });
//     //     }
//     // },

//     async refreshCartContents(data) {
//         // Update cart count
//         this.updateCartCount(data.cartCount);
        
//         // Update cart menu content
//         if (this.cartMenu) {
//             const cartContent = this.cartMenu.querySelector('.cart-content');
//             if (cartContent && data.html) {
//                 cartContent.innerHTML = data.html;
//             }

//             // Update cart total if it exists
//             const cartTotalElement = this.cartMenu.querySelector('.cart-total-amount');
//             if (cartTotalElement && data.cartTotal) {
//                 cartTotalElement.textContent = `$${parseFloat(data.cartTotal).toFixed(2)}`;
//             }
//         }
//     },

//     updateCartCount(count) {
//         if (this.navCartCount) {
//             this.navCartCount.textContent = count || '0';
//         }
//     }
// };

// // Initialize cart functionality
// document.addEventListener('DOMContentLoaded', () => {
//     CartUI.init();
// });

class CartSystem {
    constructor() {
        this.isLoading = false;
        this.cartData = {
            items: [],
            count: 0,
            total: '0.00'
        };
        this.init();
        this.setupStorageListener();

    }

    init() {
        this.loadCartData();
        this.bindEvents();
        
        // Initialize IziToast if not already done
        if (typeof iziToast !== 'undefined') {
            iziToast.settings({
                timeout: 3000,
                resetOnHover: true,
                transitionIn: 'fadeIn',
                transitionOut: 'fadeOut',
                position: 'topRight'
            });
        }
    }

    bindEvents() {
        // Listen for page load
        document.addEventListener('DOMContentLoaded', () => {
            this.loadCartData();
        });

        // Listen for auth state changes
        document.addEventListener('authStateChanged', () => {
            this.loadCartData();
        });
    }

    setupStorageListener() {
        // Listen for storage events (works across tabs)
        window.addEventListener('storage', (e) => {
            if (e.key === 'cart_updated') {
                this.loadCartData();
            }
        });
    }

    // Add after successful cart operations
    triggerCartUpdate() {
        // Trigger event for other tabs
        localStorage.setItem('cart_updated', Date.now().toString());
        localStorage.removeItem('cart_updated');
    }

    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        };

        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error('Cart request failed:', error);
            this.showNotification('error', error.message || 'Something went wrong');
            throw error;
        }
    }

    async loadCartData() {
        try {
            const data = await this.makeRequest('/cart');
            this.cartData = data;
            this.updateCartUI();
        } catch (error) {
            console.error('Failed to load cart data:', error);
        }
    }

    async addToCart(button) {
        if (this.isLoading) return;

        const productId = button.dataset.productId;
        const quantity = 1; // Default quantity, can be modified
        
        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const data = await this.makeRequest('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            this.updateCartCount();
            this.showNotification('success', data.message);
            
            // Reload full cart data
            await this.loadCartData();

            if (data.success) {
                this.trackCartEvent('add_to_cart', productId, quantity, data.cart_item.subtotal);
            }
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    async updateCartItem(itemId, quantity) {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const data = await this.makeRequest(`/cart/${itemId}`, {
                method: 'PUT',
                body: JSON.stringify({ quantity })
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            this.updateCartUI();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Reload cart data to reset UI
            this.loadCartData();
        } finally {
            this.isLoading = false;
        }
    }

    async removeFromCart(itemId) {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const data = await this.makeRequest(`/cart/${itemId}`, {
                method: 'DELETE'
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            await this.loadCartData();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.isLoading = false;
        }
    }

    async clearCart() {
        if (this.isLoading) return;
        
        // Confirm before clearing
        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }

        this.isLoading = true;

        try {
            const data = await this.makeRequest('/cart', {
                method: 'DELETE'
            });

            this.cartData = {
                items: [],
                count: 0,
                total: '0.00'
            };
            
            this.updateCartUI();
            this.showNotification('success', data.message);
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.isLoading = false;
        }
    }

    updateCartUI() {
        this.updateCartCount();
        this.updateCartItems();
        this.updateCartTotal();
        this.toggleEmptyState();
    }

    updateCartCount() {
        const countElements = document.querySelectorAll('#cart-count, .cart-count');
        countElements.forEach(element => {
            element.textContent = this.cartData.cart_count || 0;
        });
    }

    updateCartItems() {
        const cartItemsContainer = document.getElementById('cart-items');
        if (!cartItemsContainer) return;

        if (!this.cartData.cart_items || this.cartData.cart_items.length === 0) {
            cartItemsContainer.innerHTML = '';
            return;
        }

        cartItemsContainer.innerHTML = this.cartData.cart_items.map(item => this.renderCartItem(item)).join('');
    }

    renderCartItem(item) {
        return `
            <div class="cart-item" data-item-id="${item.id}">
                <div class="item-image">
                    <img src="${item.product_image || '/images/placeholder.jpg'}" alt="${item.product_name}" loading="lazy">
                </div>
                <div class="item-details">
                    <h4 class="item-name">${item.product_name}</h4>
                    <div class="item-price">$${parseFloat(item.product_price).toFixed(2)}</div>
                    ${item.product_options ? this.renderProductOptions(item.product_options) : ''}
                </div>
                <div class="item-quantity">
                    <button type="button" class="qty-btn qty-minus" onclick="Cart.changeQuantity(${item.id}, ${item.quantity - 1})">-</button>
                    <input type="number" class="qty-input" value="${item.quantity}" min="1" max="100" 
                           onchange="Cart.changeQuantity(${item.id}, this.value)" readonly>
                    <button type="button" class="qty-btn qty-plus" onclick="Cart.changeQuantity(${item.id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-total">$${(parseFloat(item.product_price) * item.quantity).toFixed(2)}</div>
                <button type="button" class="remove-item" onclick="Cart.removeFromCart(${item.id})" aria-label="Remove item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="3,6 5,6 21,6"></polyline>
                        <path d="M19,6V20a2,2,0,0,1-2,2H7a2,2,0,0,1-2-2V6M8,6V4a2,2,0,0,1,2-2h4a2,2,0,0,1,2,2V6"></path>
                    </svg>
                </button>
            </div>
        `;
    }

    renderProductOptions(options) {
        if (!options || typeof options !== 'object') return '';
        
        return `
            <div class="item-options">
                ${Object.entries(options).map(([key, value]) => 
                    `<span class="option">${key}: ${value}</span>`
                ).join(', ')}
            </div>
        `;
    }

    updateCartTotal() {
        const totalElements = document.querySelectorAll('#cart-total, .cart-total');
        totalElements.forEach(element => {
            element.textContent = `$${this.cartData.cart_total || '0.00'}`;
        });
    }

    toggleEmptyState() {
        const emptyElement = document.getElementById('cart-empty');
        const itemsElement = document.getElementById('cart-items');
        const footerElement = document.getElementById('cart-footer');
        
        if (!emptyElement || !itemsElement || !footerElement) return;

        const isEmpty = !this.cartData.cart_items || this.cartData.cart_items.length === 0;
        
        emptyElement.style.display = isEmpty ? 'block' : 'none';
        itemsElement.style.display = isEmpty ? 'none' : 'block';
        footerElement.style.display = isEmpty ? 'none' : 'block';
    }

    changeQuantity(itemId, newQuantity) {
        const quantity = parseInt(newQuantity);
        
        if (quantity < 1) {
            this.removeFromCart(itemId);
            return;
        }
        
        if (quantity > 100) {
            this.showNotification('warning', 'Maximum quantity is 100');
            return;
        }
        
        this.updateCartItem(itemId, quantity);
    }

    setButtonLoading(button, isLoading) {
        const textSpan = button.querySelector('.btn-text');
        const loadingSpan = button.querySelector('.btn-loading');
        
        if (textSpan && loadingSpan) {
            textSpan.style.display = isLoading ? 'none' : 'inline';
            loadingSpan.style.display = isLoading ? 'inline' : 'none';
        }
        
        button.disabled = isLoading;
    }

    showNotification(type, message) {
        if (typeof iziToast !== 'undefined') {
            iziToast[type]({
                title: type.charAt(0).toUpperCase() + type.slice(1),
                message: message
            });
        } else {
            // Fallback to alert if iziToast is not available
            alert(message);
        }
    }

    proceedToCheckout() {
        if (!this.cartData.cart_items || this.cartData.cart_items.length === 0) {
            this.showNotification('warning', 'Your cart is empty');
            return;
        }
        
        // Redirect to checkout page
        window.location.href = '/checkout';
    }

    addToCartWithVariants(button) {
        const productCard = button.closest('[data-product-id]');
        const productId = productCard.dataset.productId;
        const quantityInput = productCard.querySelector('.product-quantity');
        const variantSelects = productCard.querySelectorAll('.variant-select');
        
        // Collect variants
        const variants = {};
        let allVariantsSelected = true;
        
        variantSelects.forEach(select => {
            if (select.value) {
                variants[select.dataset.variant] = {
                    value: select.value,
                    label: select.selectedOptions[0].text
                };
            } else {
                allVariantsSelected = false;
            }
        });
        
        if (variantSelects.length > 0 && !allVariantsSelected) {
            this.showNotification('warning', 'Please select all product options');
            return;
        }
        
        const quantity = parseInt(quantityInput?.value || 1);
        
        this.addToCartWithOptions(button, productId, quantity, variants);
    }

    async addToCartWithOptions(button, productId, quantity, options = {}) {
        if (this.isLoading) return;
        
        this.setButtonLoading(button, true);
        this.isLoading = true;

        try {
            const data = await this.makeRequest('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    product_options: options
                })
            });

            this.cartData.count = data.cart_count;
            this.cartData.total = data.cart_total;
            
            this.updateCartCount();
            this.showNotification('success', data.message);
            
            await this.loadCartData();
            
        } catch (error) {
            // Error handling is done in makeRequest
        } finally {
            this.setButtonLoading(button, false);
            this.isLoading = false;
        }
    }

    trackCartEvent(action, productId, quantity = 1, value = 0) {
    // Google Analytics 4
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            currency: 'USD',
            value: value,
            items: [{
                item_id: productId,
                quantity: quantity
            }]
        });
    }
    
    // Facebook Pixel
    if (typeof fbq !== 'undefined') {
            fbq('track', action, {
                content_ids: [productId],
                content_type: 'product',
                value: value,
                currency: 'USD'
            });
        }
    }
}


class CartUISystem {
    constructor() {
        this.isOpen = false;
        this.init();
    }

    init() {
        // Bind escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeCart();
            }
        });
    }

    toggleCart() {
        if (this.isOpen) {
            this.closeCart();
        } else {
            this.openCart();
        }
    }

    openCart() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartMenu && cartOverlay) {
            cartMenu.classList.add('cart-open');
            cartOverlay.classList.add('overlay-active');
            document.body.classList.add('cart-menu-open');
            this.isOpen = true;
            
            // Load fresh cart data when opening
            Cart.loadCartData();
        }
    }

    closeCart() {
        const cartMenu = document.getElementById('cart-menu');
        const cartOverlay = document.getElementById('cart-overlay');
        
        if (cartMenu && cartOverlay) {
            cartMenu.classList.remove('cart-open');
            cartOverlay.classList.remove('overlay-active');
            document.body.classList.remove('cart-menu-open');
            this.isOpen = false;
        }
    }
}

// Initialize cart systems
const Cart = new CartSystem();
const CartUI = new CartUISystem();

// Make cart available globally
window.Cart = Cart;
window.CartUI = CartUI;