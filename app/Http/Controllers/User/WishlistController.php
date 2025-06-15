<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

use function PHPSTORM_META\elementType;

class WishlistController extends Controller
{
    public function index()
    {
        // Get authenticated user's wishlist items
        // with pagination (12 items per page)
        $user = auth()->user();
        $wishlists = $user->wishlistProducts();

        return view('user.wishlists', compact('wishlists', 'user'));
    }

    public function toggle(Product $product, Request $request)
    {
        try {
            if (!$product) {
                // \Log::error('Product not found');
                return response()->json(['error' => 'Product not found'], 404);
            }

            $user = $request->user();
            if (!$user) {
                // \Log::error('No authenticated user found');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $wishlist = Wishlist::where('user_id', $user->id)
                            ->where('product_id', $product->id)
                            ->first();

            if ($wishlist) {
                $wishlist->delete();
                // \Log::info('Product removed from wishlist: ' . $product->id);
                return response()->json([
                    'status' => 'removed',
                    'message' => 'Product removed from wishlist',
                    'count' => $user->wishlists()->count()

                ]);
            }

            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            
            // \Log::info('Product added to wishlist: ' . $product->id);
            return response()->json([
                'status' => 'added',
                'message' => 'Product added to wishlist',
                'count' => $user->wishlists()->count()

            ]);

        } catch (\Exception $e) {
            // \Log::error('Wishlist toggle error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
