<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function add(Request $request): JsonResponse
    {
        $cartItem = CartItem::find($request->cart_item_id);
        $productId = $cartItem ? $cartItem->product_id : null;
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'integer|min:1|max:99',
            'options' => 'array'
        ]);

        $success = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity ?? 1,
            $request->options ?? []
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart' => $this->cartService->getCartSummary(),
                'inCart' => true,
                'product_id' => $productId
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to add product to cart'
        ], 400);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:99'
        ]);

        $success = $this->cartService->updateQuantity(
            $request->cart_item_id,
            $request->quantity
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cartItem' => $this->cartService->getCartSummary()
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update cart'
        ], 400);
    }

    public function remove(Request $request): JsonResponse
    {
        $cartItem = CartItem::find($request->cart_item_id);
        $productId = $cartItem ? $cartItem->product_id : null;

        $request->validate([
            'cart_item_id' => 'required|integer'
        ]);

        $success = $this->cartService->removeFromCart($request->cart_item_id);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart' => $this->cartService->getCartSummary(),
                'product_id' => $productId
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove item'
        ], 400);
    }

    public function clear(): JsonResponse
    {
        $success = $this->cartService->clearCart();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Cart cleared' : 'Failed to clear cart',
            'cart' => $this->cartService->getCartSummary()
        ]);
    }

    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'cart' => $this->cartService->getCartSummary()
        ]);
    }
}