<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }
        
        return ['session_id' => Session::getId()];
    }

    public function getCartItems()
    {
        return CartItem::where($this->getCartIdentifier())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addToCart(int $productId, int $quantity = 1, array $options = []): bool
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return false;
        }

        $identifier = $this->getCartIdentifier();
        $cartItem = CartItem::where($identifier)
            ->where('product_id', $productId)
            ->where('product_options', json_encode($options))
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create(array_merge($identifier, [
                'product_id' => $productId,
                'product_name' => $product->title,
                'price' => $product->price,
                'quantity' => $quantity,
                'product_options' => $options
            ]));
        }

        return true;
    }

    public function isInCart(int $productId): bool
    {
        return CartItem::where($this->getCartIdentifier())
            ->where('product_id', $productId)
            ->exists();
    }

    public function updateQuantity(int $cartItemId, int $quantity): bool
    {
        $cartItem = CartItem::where($this->getCartIdentifier())
            ->where('id', $cartItemId)
            ->first();

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($cartItemId);
        }

        $cartItem->update(['quantity' => $quantity]);
        return true;
    }

    public function removeFromCart(int $cartItemId): bool
    {
        return CartItem::where($this->getCartIdentifier())
            ->where('id', $cartItemId)
            ->delete() > 0;
    }

    public function clearCart(): bool
    {
        return CartItem::where($this->getCartIdentifier())->delete() > 0;
    }

    public function getCartCount(): int
    {
        return CartItem::where($this->getCartIdentifier())->sum('quantity');
    }

    public function getCartTotal(): float
    {
        return CartItem::where($this->getCartIdentifier())
            ->get()
            ->sum('subtotal');
    }

    public function getCartSummary(): array
    {
        $items = $this->getCartItems();
        
        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'total' => $items->sum('subtotal'),
            'isEmpty' => $items->isEmpty()
        ];
    }

    public function mergeGuestCart(string $sessionId): void
    {
        if (!Auth::check()) {
            return;
        }

        $guestItems = CartItem::where('session_id', $sessionId)->get();
        
        foreach ($guestItems as $guestItem) {
            $existingItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $guestItem->product_id)
                ->where('product_options', $guestItem->product_options)
                ->first();

            if ($existingItem) {
                $existingItem->increment('quantity', $guestItem->quantity);
            } else {
                $guestItem->update([
                    'user_id' => Auth::id(),
                    'session_id' => null
                ]);
            }
        }

        CartItem::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();
    }
}