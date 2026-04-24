<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $search = trim(request()->string('search')->toString());
        $normalizedSearch = strtolower($search);

        return view('admin.coupons.index', [
            'coupons' => Coupon::query()
                ->when($search !== '', function ($query) use ($search, $normalizedSearch) {
                    $query->where(function ($subQuery) use ($search, $normalizedSearch) {
                        $subQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");

                        if (str_contains($normalizedSearch, 'inactive')) {
                            $subQuery->orWhere('is_active', false);
                        } elseif (str_contains($normalizedSearch, 'active')) {
                            $subQuery->orWhere('is_active', true);
                        }
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.create', ['coupon' => new Coupon()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCoupon($request);

        Coupon::create($validated + [
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $this->validateCoupon($request, $coupon);

        $coupon->update($validated + [
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    protected function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:coupons,code,'.($coupon?->id ?? 'NULL').',id'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
