<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with(['user', 'items', 'payment', 'shipment'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('order_number', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhere('payment_status', 'like', "%{$search}%")
                            ->orWhere('fulfillment_status', 'like', "%{$search}%")
                            ->orWhere('shipping_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('items', function ($itemsQuery) use ($search) {
                                $itemsQuery->where('product_title', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'payment', 'shipment']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'string', 'max:50'],
            'fulfillment_status' => ['required', 'string', 'max:50'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'carrier' => ['nullable', 'string', 'max:255'],
            'shipment_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'fulfillment_status' => $validated['fulfillment_status'],
            'shipping_fee' => $validated['shipping_fee'] ?? $order->shipping_fee,
            'notes' => $validated['notes'] ?? null,
            'confirmed_at' => $validated['status'] === 'confirmed' ? ($order->confirmed_at ?? Carbon::now()) : $order->confirmed_at,
            'shipped_at' => $validated['fulfillment_status'] === 'shipped' ? ($order->shipped_at ?? Carbon::now()) : $order->shipped_at,
            'delivered_at' => $validated['fulfillment_status'] === 'delivered' ? ($order->delivered_at ?? Carbon::now()) : $order->delivered_at,
            'cancelled_at' => $validated['status'] === 'cancelled' ? Carbon::now() : null,
        ]);

        $shipment = $order->shipment()->firstOrCreate([], [
            'status' => $validated['fulfillment_status'],
            'shipping_fee' => $validated['shipping_fee'] ?? $order->shipping_fee,
        ]);

        $shipment->update([
            'carrier' => $validated['carrier'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'status' => $validated['fulfillment_status'],
            'shipping_fee' => $validated['shipping_fee'] ?? $shipment->shipping_fee,
            'notes' => $validated['shipment_notes'] ?? null,
            'shipped_at' => $validated['fulfillment_status'] === 'shipped' ? ($shipment->shipped_at ?? Carbon::now()) : $shipment->shipped_at,
            'delivered_at' => $validated['fulfillment_status'] === 'delivered' ? ($shipment->delivered_at ?? Carbon::now()) : $shipment->delivered_at,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
