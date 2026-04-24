<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        return view('admin.reviews.index', [
            'reviews' => Review::query()
                ->with(['product', 'user'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('body', 'like', "%{$search}%")
                            ->orWhereHas('product', function ($productQuery) use ($search) {
                                $productQuery->where('title', 'like', "%{$search}%");
                            })
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });

                        if (str_contains(strtolower($search), 'approved')) {
                            $subQuery->orWhere('is_approved', true);
                        }

                        if (str_contains(strtolower($search), 'pending')) {
                            $subQuery->orWhere('is_approved', false);
                        }
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $review->update([
            'is_approved' => (bool) ($validated['is_approved'] ?? false),
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
}
