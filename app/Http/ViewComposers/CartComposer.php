<?php

namespace App\Http\ViewComposers;

use App\Services\CartService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;


class CartComposer 
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function compose(View $view)
    {
        try {
            $view->with([
                'cartItems' => $this->cartService->getCartItems(),
                'cartTotal' => $this->cartService->getCartTotal(),
                'cartCount' => $this->cartService->getCartCount()
            ]);
        } catch (\Exception $e) {
            // Fallback values in case of error
            Log::error('CartComposer error: ' . $e->getMessage());
            $view->with([
                'cartItems' => [],
                'cartTotal' => 0,
                'cartCount' => 0
            ]);
        }
    }
}