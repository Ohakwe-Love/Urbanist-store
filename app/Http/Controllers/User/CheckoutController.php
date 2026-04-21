<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCart();
        $cartData = $this->cartService->getCartData();
        $subtotal = (float) $cartData['total'];
        $shipping = $subtotal === 0.0 ? 0.0 : ($subtotal >= 1000 ? 0.0 : 45.0);
        $tax = round($subtotal * 0.075, 2);
        $discount = 0.0;
        $total = $subtotal + $shipping + $tax - $discount;

        return view('user.checkout', [
            'cartItems' => $cart->items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]);
    }


    // public function checkoutMethod(): JsonResponse
    // {
    //     try {
    //         $cart = $this->cartService->getCart();

    //         if ($cart->items->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Your cart is empty. Please add items before proceeding to checkout.'
    //             ], 400);
    //         }

    //         // Here you would typically prepare data for the checkout view
    //         // For simplicity, we just return the cart data

    //         return response()->json([
    //             'success' => true,
    //             'cart' => $cart
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Checkout error: ' . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
