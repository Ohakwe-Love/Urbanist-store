<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function summary(): JsonResponse
    {
        try {
            $data = $this->cartService->getCartData();
            $cart = $this->cartService->getCart();

            // Generate HTML for cart menu CONTENT only (not the wrapper)
            $html = view('components.cart-menu-items', [
                'cartItems' => $cart->items,
                'cartTotal' => $data['total'],
                'cartCount' => $data['count']
            ])->render();

            return response()->json([
                'success' => true,
                'cart' => $data,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart summary error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->input('quantity', 1);

            $data = $this->cartService->addItem($product, $quantity);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $data = $this->cartService->updateQuantity(
                $request->cart_item_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => $request->quantity > 0 ? 'Cart updated successfully' : 'Item removed from cart',
                'cart' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer'
        ]);

        try {
            $data = $this->cartService->removeItem($request->cart_item_id);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'cart' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $data = $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
