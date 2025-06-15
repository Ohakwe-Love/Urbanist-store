<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Events\CartUpdated;

class CartService
{
    protected Cart $cart;

    public function __construct()
    {
        $this->cart = $this->getOrCreateCart();
    }

    public function getCart(): Cart
    {
        return $this->cart->load(['items.product', 'items.variant']);
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        // Validate stock availability
        $this->validateStock($product, $variant, $quantity);

        // Check if item already exists in cart
        $existingItem = $this->cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existingItem) {
            return $this->updateItemQuantity($existingItem->id, $existingItem->quantity + $quantity);
        }

        // Create new cart item
        $unitPrice = $this->calculatePrice($product, $variant);
        $cartItem = $this->cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'product_snapshot' => $this->createProductSnapshot($product, $variant)
        ]);

        $this->broadcastCartUpdate();
        
        return $cartItem;
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->cart->items()->findOrFail($cartItemId);
        
        if ($quantity <= 0) {
            return $this->removeItem($cartItemId);
        }

        // Validate stock
        $this->validateStock($cartItem->product, $cartItem->variant, $quantity);

        $cartItem->update([
            'quantity' => $quantity,
            'total_price' => $cartItem->unit_price * $quantity
        ]);

        $this->broadcastCartUpdate();
        
        return $cartItem;
    }

    public function removeItem(int $cartItemId): bool
    {
        $cartItem = $this->cart->items()->findOrFail($cartItemId);
        $result = $cartItem->delete();
        
        $this->broadcastCartUpdate();
        
        return $result;
    }

    public function clearCart(): bool
    {
        $result = $this->cart->items()->delete();
        $this->broadcastCartUpdate();
        
        return $result;
    }

    public function syncPrices(): void
    {
        $this->cart->items->each(function (CartItem $item) {
            $currentPrice = $this->calculatePrice($item->product, $item->variant);
            
            if ($item->unit_price != $currentPrice) {
                $item->update([
                    'unit_price' => $currentPrice,
                    'total_price' => $currentPrice * $item->quantity
                ]);
            }
        });

        $this->broadcastCartUpdate();
    }

    public function validateCartItems(): array
    {
        $issues = [];

        $this->cart->items->each(function (CartItem $item) use (&$issues) {
            // Check if product is still available
            if (!$item->product->is_active) {
                $issues[] = [
                    'type' => 'unavailable',
                    'item_id' => $item->id,
                    'message' => "Product '{$item->product->name}' is no longer available"
                ];
                return;
            }

            // Check stock availability
            if (!$item->hasStockAvailable()) {
                $availableStock = $item->variant 
                    ? $item->variant->stock_quantity 
                    : $item->product->stock_quantity;
                    
                $issues[] = [
                    'type' => 'insufficient_stock',
                    'item_id' => $item->id,
                    'available_quantity' => $availableStock,
                    'message' => "Only {$availableStock} items available for '{$item->product->name}'"
                ];
            }

            // Check price changes
            $currentPrice = $this->calculatePrice($item->product, $item->variant);
            if ($item->unit_price != $currentPrice) {
                $issues[] = [
                    'type' => 'price_change',
                    'item_id' => $item->id,
                    'old_price' => $item->unit_price,
                    'new_price' => $currentPrice,
                    'message' => "Price changed for '{$item->product->name}'"
                ];
            }
        });

        return $issues;
    }

    public function mergeCarts(Cart $guestCart): void
    {
        if (!Auth::check()) {
            return;
        }

        DB::transaction(function () use ($guestCart) {
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $this->cart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where('variant_id', $guestItem->variant_id)
                    ->first();

                if ($existingItem) {
                    $newQuantity = $existingItem->quantity + $guestItem->quantity;
                    $this->updateItemQuantity($existingItem->id, $newQuantity);
                } else {
                    $this->addItem(
                        $guestItem->product_id,
                        $guestItem->quantity,
                        $guestItem->variant_id
                    );
                }
            }

            $guestCart->delete();
        });
    }

    protected function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['expires_at' => now()->addDays(30)]
            );
        }

        $sessionId = Session::getId();
        
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['expires_at' => now()->addDays(7)]
        );
    }

    protected function validateStock(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
        
        if ($quantity > $availableStock) {
            throw new \Exception("Insufficient stock. Only {$availableStock} items available.");
        }
    }

    protected function calculatePrice(Product $product, ?ProductVariant $variant): float
    {
        $basePrice = $product->price;
        $variantAdjustment = $variant?->price_adjustment ?? 0;
        
        // Apply any active promotions here
        // $promotionalPrice = $this->applyPromotions($basePrice + $variantAdjustment, $product);
        
        return $basePrice + $variantAdjustment;
    }

    protected function createProductSnapshot(Product $product, ?ProductVariant $variant): array
    {
        return [
            'name' => $product->name,
            'image' => $product->featured_image,
            'variant_name' => $variant?->name,
            'variant_attributes' => $variant?->attributes,
            'captured_at' => now()->toISOString()
        ];
    }

    protected function broadcastCartUpdate(): void
    {
        broadcast(new CartUpdated($this->cart))->toOthers();
    }
}