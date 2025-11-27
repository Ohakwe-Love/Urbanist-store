<?php

// namespace App\View\Components;

// use Closure;
// use Illuminate\Contracts\View\View;
// use Illuminate\View\Component;
// use App\Services\CartService;

// class CartMenu extends Component
// {
//     /**
//      * Create a new component instance.
//      */
//     public $cartItems;
//     public $cartTotal;
//     public $cartCount;
//     public $isAjax;


//     public function __construct(CartService $cartService)
//     {
        
//         $this->cartItems = $cartService->getCart();
//         $this->cartTotal = $cartService->getCartData();
//         $this->cartCount = $cartService->getCartData();
//         $this->isAjax = request()->ajax();
//     }

//     /**
//      * Get the view / contents that represent the component.
//      */
//     public function render(): View|Closure|string
//     {
//         // if ($this->isAjax) {
//         //     return view('components.cart-menu-content');
//         // }
//         return view('components.cart-menu');
//     }
// }


namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\CartService;

class CartMenu extends Component
{
    public $cartItems;
    public $cartTotal;
    public $cartCount;
    public $isAjax;

    public function __construct(CartService $cartService)
    {
        // Get the Cart model with items loaded
        $cart = $cartService->getCart();
        
        // Get the formatted cart data
        $cartData = $cartService->getCartData();
        
        // Assign correct values
        $this->cartItems = $cart->items; // Collection of CartItem models
        $this->cartTotal = $cartData['total']; // Numeric total
        $this->cartCount = $cartData['count']; // Item count
        $this->isAjax = request()->ajax();
    }

    public function render(): View|Closure|string
    {
        return view('components.cart-menu');
    }
}