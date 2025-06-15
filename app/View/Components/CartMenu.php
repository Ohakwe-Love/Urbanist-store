<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\CartService;

class CartMenu extends Component
{
    /**
     * Create a new component instance.
     */
    public $cartItems;
    public $cartTotal;
    public $cartCount;
    public $isAjax;


    public function __construct(CartService $cartService)
    {
        $this->cartItems = $cartService->getCartItems();
        $this->cartTotal = $cartService->getCartTotal();
        $this->cartCount = $cartService->getCartCount();
        $this->isAjax = request()->ajax();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if ($this->isAjax) {
            return view('components.cart-menu-content');
        }
        return view('components.cart-menu');
    }
}
