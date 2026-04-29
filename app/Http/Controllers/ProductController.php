<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 6; // Number of products per page
        $page = $request->get('page', 1);
        
        // Get all unique categories for the sidebar
        $categories = Product::visibleOnStorefront()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
        
        // Build the query
        $query = Product::visibleOnStorefront();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($sub) use ($search) {
                $sub->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
                // Optional: handle "in stock" or "sold out" keywords
                if (stripos($search, 'in stock') !== false) {
                    $sub->orWhere('stock_quantity', '>', 0);
                }
            });
        }
        
        // Apply filters based on request parameters (only one filter at a time)
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        } elseif ($request->has('availability') && $request->availability) {
            if ($request->availability === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->availability === 'sold_out') {
                $query->where('stock_quantity', '<=', 0);
            }
        } elseif ($request->has('size') && $request->size) {
            $query->where('size', $request->size);
        } elseif ($request->has('min_price') || $request->has('max_price')) {
            $minPrice = $request->get('min_price', 0);
            $maxPrice = $request->get('max_price', 999999);
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }
        
        // Get total count for pagination logic
        $totalProducts = $query->count();
        
        // Apply pagination
        $products = $query->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
        
        // Calculate if there are more products to load
        $hasMoreProducts = ($page * $perPage) < $totalProducts;
        
        // Handle AJAX requests for load more functionality
        if ($request->ajax()) {
            return response()->json([
                'products' => view('components.product-row', compact('products'))->render(),
                'hasMore' => $hasMoreProducts,
                'currentPage' => $page,
                'totalProducts' => $totalProducts
            ]);
        }
        
        return view('pages.shop', compact(
            'products', 
            'categories', 
            'hasMoreProducts', 
            'totalProducts'
        ));
    }

    public function show(Product $product)
    {
        abort_unless($product->isVisibleOnStorefront(), 404);

        $product->load([
            'images',
            'specifications',
            'reviews' => fn ($query) => $query
                ->where('is_approved', true)
                ->with('user')
                ->latest(),
        ]);

        $approvedReviews = $product->reviews;
        $reviewCount = $approvedReviews->count();
        $averageRating = $reviewCount > 0 ? round((float) $approvedReviews->avg('rating'), 1) : 0.0;
        $ratingBreakdown = collect(range(5, 1))
            ->mapWithKeys(fn (int $rating) => [
                $rating => $reviewCount > 0
                    ? (int) round(($approvedReviews->where('rating', $rating)->count() / $reviewCount) * 100)
                    : 0,
            ]);

        $userReview = null;
        $canReview = false;

        if (auth()->check()) {
            $userReview = $product->reviews()
                ->withoutGlobalScopes()
                ->where('user_id', auth()->id())
                ->latest()
                ->first();

            $canReview = auth()->user()->orders()
                ->where('payment_status', 'paid')
                ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
                ->exists();
        }

        return view('products.show', compact(
            'product',
            'approvedReviews',
            'reviewCount',
            'averageRating',
            'ratingBreakdown',
            'userReview',
            'canReview'
        ));
    }
}
