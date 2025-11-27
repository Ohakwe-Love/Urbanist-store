<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart  // Change from private to public
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            $this->mergeSessionCart($cart);
        } else {
            $sessionId = Session::getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }

        return $cart->load('items.product');
    }

    public function addItem(Product $product, int $quantity = 1): array
    {
        $cart = $this->getCart();

        if ($product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;

            if ($product->stock_quantity < $newQuantity) {
                throw new \Exception('Insufficient stock available');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price
            ]);
        }

        return $this->getCartData();
    }

    // public function updateQuantity(int $cartItemId, int $quantity): array
    // {
    //     $cart = $this->getCart();
    //     $cartItem = CartItem::where('cart_id', $cart->id)
    //         ->findOrFail($cartItemId);

    //     if ($quantity <= 0) {
    //         $cartItem->delete();
    //         return $this->getCartData();
    //     }

    //     if ($cartItem->product->stock_quantity < $quantity) {
    //         throw new \Exception('Insufficient stock available');
    //     }

    //     $cartItem->update(['quantity' => $quantity]);

    //     return $this->getCartData();
    // }

    public function updateQuantity(int $cartItemId, int $quantity): array
    {
        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            return $this->getCartData();
        }

        if ($cartItem->product->stock_quantity < $quantity) { // Fixed typo
            throw new \Exception('Insufficient stock available');
        }

        $cartItem->update(['quantity' => $quantity]);

        return $this->getCartData();
    }

    public function removeItem(int $cartItemId): array
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->delete();

        return $this->getCartData();
    }

    public function clearCart(): array
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return $this->getCartData();
    }

    public function getCartData(): array
    {
        $cart = $this->getCart();

        return [
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->title,
                    'product_image' => $item->product->image_url ?? null,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'stock' => $item->product->stock_quantity,
                ];
            }),
            'total' => $cart->total,
            'count' => $cart->item_count,
            'product_ids' => $cart->items->pluck('product_id')->toArray(),
        ];
    }

    private function mergeSessionCart(Cart $userCart): void
    {
        $sessionId = Session::getId();
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if ($sessionCart && $sessionCart->id !== $userCart->id) {
            foreach ($sessionCart->items as $item) {
                $existingItem = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $item->quantity
                    ]);
                } else {
                    CartItem::create([
                        'cart_id' => $userCart->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }
            }

            $sessionCart->delete();
        }
    }
}
