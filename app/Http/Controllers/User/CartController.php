<?php


namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show(): JsonResponse
    {
        $cart = $this->cartService->getCart();
        
        return response()->json([
            'cart' => [
                'id' => $cart->id,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'image' => $item->product->featured_image,
                        ],
                        'variant' => $item->variant ? [
                            'id' => $item->variant->id,
                            'name' => $item->variant->name,
                            'attributes' => $item->variant->attributes,
                        ] : null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ];
                }),
                'total' => $cart->total,
                'item_count' => $cart->item_count,
            ]
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $cartItem = $this->cartService->addItem(
                $request->product_id,
                $request->quantity,
                $request->variant_id
            );

            return response()->json([
                'message' => 'Item added to cart successfully',
                'cart_item' => $cartItem
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateItem(Request $request, int $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:10'
        ]);

        try {
            if ($request->quantity == 0) {
                $this->cartService->removeItem($cartItemId);
                return response()->json(['message' => 'Item removed from cart']);
            }

            $cartItem = $this->cartService->updateItemQuantity($cartItemId, $request->quantity);

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cart_item' => $cartItem
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function removeItem(int $cartItemId): JsonResponse
    {
        try {
            $this->cartService->removeItem($cartItemId);

            return response()->json([
                'message' => 'Item removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function clear(): JsonResponse
    {
        $this->cartService->clearCart();

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function validate(): JsonResponse
    {
        $issues = $this->cartService->validateCartItems();

        return response()->json([
            'valid' => empty($issues),
            'issues' => $issues
        ]);
    }

    public function syncPrices(): JsonResponse
    {
        $this->cartService->syncPrices();

        return response()->json([
            'message' => 'Cart prices synchronized successfully'
        ]);
    }
}













































































































// class CartController extends Controller
// {
//     protected $cartService;

//     public function __construct(CartService $cartService)
//     {
//         $this->cartService = $cartService;
//     }

//     public function index()
//     {
//         try {
//             if (request()->ajax()) {
//                 return response()->json([
//                     'success' => true,
//                     'cartItems' => $this->cartService->getCartItems(),
//                     'cartTotal' => $this->cartService->getCartTotal(),
//                     'cartCount' => $this->cartService->getCartCount()
//                 ]);
//             }

//             return response()->json(['error' => 'Direct access not allowed'], 403);
//         } catch (\Exception $e) {
//             Log::error('Cart index error: ' . $e->getMessage());
//             return response()->json(['error' => 'Something went wrong'], 500);
//         }
//     }

//     public function menu()
//     {
//         try {
//             if (!request()->ajax()) {
//                 return response()->json(['error' => 'Direct access not allowed'], 403);
//             }

//             $cartItems = $this->cartService->getCartItems();
//             $cartTotal = $this->cartService->getCartTotal();
//             $cartCount = $this->cartService->getCartCount();

//             // Return both HTML and data
//             return response()->json([
//                 'success' => true,
//                 'html' => view('components.cart-menu-content', compact('cartItems', 'cartTotal'))->render(),
//                 'cartItems' => $cartItems,
//                 'cartTotal' => $cartTotal,
//                 'cartCount' => $cartCount
//             ]);
//         } catch (\Exception $e) {
//             Log::error('Cart menu error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Failed to load cart menu'
//             ], 500);
//         }
//     }

//     // public function add(Request $request, Product $product)
//     // {
//     //     try {
//     //         $request->validate([
//     //             'quantity' => 'required|integer|min:1'
//     //         ]);

//     //         if (!$product->isInStock($request->quantity)) {
//     //             return response()->json([
//     //                 'success' => false,
//     //                 'message' => 'Insufficient stock'
//     //             ], 422);
//     //         }

//     //         $this->cartService->addToCart($product, $request->quantity);

//     //         return response()->json([
//     //             'success' => true,
//     //             'message' => 'Product added to cart',
//     //             'cartCount' => $this->cartService->getCartCount()
//     //         ]);

//     //     } catch (\Exception $e) {
//     //         Log::error('Cart add error: ' . $e->getMessage());
//     //         return response()->json([
//     //             'success' => false,
//     //             'message' => 'Something went wrong. Please try again.'
//     //         ], 500);
//     //     }
//     // }

    
//     public function add(Request $request, Product $product)
//     {
//         try {
//             $request->validate([
//                 'quantity' => 'required|integer|min:1'
//             ]);

//             if (!$product->isInStock($request->quantity)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Insufficient stock'
//                 ], 422);
//             }

//             $this->cartService->addToCart($product, $request->quantity);

//             // Return all necessary data for cart refresh
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Product added to cart',
//                 'cartCount' => $this->cartService->getCartCount(),
//                 'cartItems' => $this->cartService->getCartItems(),
//                 'cartTotal' => $this->cartService->getCartTotal(),
//                 'html' => view('components.cart-menu-content', [
//                     'cartItems' => $this->cartService->getCartItems(),
//                     'cartTotal' => $this->cartService->getCartTotal()
//                 ])->render()
//             ]);

//         } catch (\Exception $e) {
//             Log::error('Cart add error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Something went wrong. Please try again.'
//             ], 500);
//         }
//     }
    
//     public function update(Request $request, Product $product)
//     {
//         try {
//             $request->validate([
//                 'quantity' => 'required|integer|min:0'
//             ]);

//             if (!$product->isInStock($request->quantity)) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Insufficient stock'
//                 ], 422);
//             }

//             $this->cartService->updateQuantity($product, $request->quantity);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Cart updated',
//                 'cartTotal' => $this->cartService->getCartTotal(),
//                 'cartCount' => $this->cartService->getCartCount()
//             ]);

//         } catch (\Exception $e) {
//             Log::error('Cart update error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Something went wrong. Please try again.'
//             ], 500);
//         }
//     }

//     // public function remove(Product $product)
//     // {
//     //     try {
//     //         $this->cartService->removeFromCart($product);

//     //         return response()->json([
//     //             'success' => true,
//     //             'message' => 'Item removed from cart',
//     //             'cartTotal' => $this->cartService->getCartTotal(),
//     //             'cartCount' => $this->cartService->getCartCount()
//     //         ]);

//     //     } catch (\Exception $e) {
//     //         Log::error('Cart remove error: ' . $e->getMessage());
//     //         return response()->json([
//     //             'success' => false,
//     //             'message' => 'Something went wrong. Please try again.'
//     //         ], 500);
//     //     }
//     // }

    
//     public function remove(Product $product)
//     {
//         try {
//             $this->cartService->removeFromCart($product);

//             // Return all necessary data for cart refresh
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Item removed from cart',
//                 'cartCount' => $this->cartService->getCartCount(),
//                 'cartItems' => $this->cartService->getCartItems(),
//                 'cartTotal' => $this->cartService->getCartTotal(),
//                 'html' => view('components.cart-menu-content', [
//                     'cartItems' => $this->cartService->getCartItems(),
//                     'cartTotal' => $this->cartService->getCartTotal()
//                 ])->render()
//             ]);

//         } catch (\Exception $e) {
//             Log::error('Cart remove error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Something went wrong. Please try again.'
//             ], 500);
//         }
//     }
    
//     public function clear()
//     {
//         try {
//             $this->cartService->clearCart();

//             if (request()->ajax()) {
//                 return response()->json([
//                     'success' => true,
//                     'message' => 'Cart cleared',
//                     'cartCount' => 0,
//                     'cartTotal' => 0,
//                     'cartItems' => []
//                 ]);
//             }

//             return redirect()->back()->with('success', 'Cart cleared');
//         } catch (\Exception $e) {
//             Log::error('Cart clear error: ' . $e->getMessage());
            
//             if (request()->ajax()) {
//                 return response()->json([
//                     'success' => false,
//                     'message' => 'Failed to clear cart'
//                 ], 500);
//             }

//             return redirect()->back()->with('error', 'Failed to clear cart');
//         }
//     }

//     public function count()
//     {
//         try {
//             return response()->json([
//                 'success' => true,
//                 'count' => $this->cartService->getCartCount()
//             ]);
//         } catch (\Exception $e) {
//             Log::error('Cart count error: ' . $e->getMessage());
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Something went wrong. Please try again.'
//             ], 500);
//         }
//     }
// }