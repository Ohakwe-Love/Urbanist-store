<?php
namespace App\View\Composers;

use App\Services\CartService;
use Illuminate\View\View;

class CartComposer
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function compose(View $view): void
    {
        $view->with('cartData', $this->cartService->getCartSummary());
    }
}