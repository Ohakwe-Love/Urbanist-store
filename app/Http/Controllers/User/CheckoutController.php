<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Models\User;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index()
    {
        return view('user.checkout');
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
