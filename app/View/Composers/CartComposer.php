<?php
namespace App\View\Composers;

use App\Services\CartService;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartComposer
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function compose(View $view): void
    {
        if (Str::startsWith($view->name(), 'admin.')) {
            $view->with([
                'cartData' => ['items' => collect(), 'total' => 0, 'count' => 0, 'product_ids' => []],
                'cartItemsLookup' => [],
                'cartProductIds' => [],
            ]);

            return;
        }

        // Get full cart data
        $cartData = $this->cartService->getCartData();
        
        // Create a lookup array for quick checks: [product_id => cart_item_id]
        $cartItemsLookup = collect($cartData['items'])->pluck('id', 'product_id')->toArray();
        
        // Share with all views
        $view->with([
            'cartData' => $cartData,
            'cartItemsLookup' => $cartItemsLookup, // For O(1) lookup
            'cartProductIds' => $cartData['product_ids'], // Array of product IDs in cart
        ]);
    }
}
