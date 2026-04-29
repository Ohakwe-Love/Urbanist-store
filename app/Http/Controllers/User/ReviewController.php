<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ProductReviewRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        $hasPurchasedProduct = $user->orders()
            ->where('payment_status', 'paid')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchasedProduct) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        $review = Review::firstOrNew([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);

        $review->fill([
            'rating' => $request->integer('rating'),
            'title' => $request->string('title')->trim()->toString() ?: null,
            'body' => $request->string('body')->trim()->toString(),
            // Any new or edited review returns to moderation.
            'is_approved' => false,
        ]);

        $review->save();

        return back()->with('success', $review->wasRecentlyCreated
            ? 'Your review has been submitted and is waiting for approval.'
            : 'Your review has been updated and sent back for approval.');
    }
}
