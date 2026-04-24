<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        return view('admin.shipments.index', [
            'shipments' => Shipment::query()
                ->with('order')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('carrier', 'like', "%{$search}%")
                            ->orWhere('tracking_number', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('order', function ($orderQuery) use ($search) {
                                $orderQuery->where('order_number', 'like', "%{$search}%")
                                    ->orWhere('shipping_name', 'like', "%{$search}%");
                            });
                    });
                })
                ->latest()
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function edit(Shipment $shipment): View
    {
        $shipment->load('order');

        return view('admin.shipments.edit', compact('shipment'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'carrier' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $shipment->update([
            'carrier' => $validated['carrier'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? null,
            'status' => $validated['status'],
            'shipping_fee' => $validated['shipping_fee'] ?? $shipment->shipping_fee,
            'notes' => $validated['notes'] ?? null,
            'shipped_at' => $validated['status'] === 'shipped' ? ($shipment->shipped_at ?? Carbon::now()) : $shipment->shipped_at,
            'delivered_at' => $validated['status'] === 'delivered' ? ($shipment->delivered_at ?? Carbon::now()) : $shipment->delivered_at,
        ]);

        if ($shipment->order) {
            $shipment->order->update([
                'fulfillment_status' => $validated['status'],
            ]);
        }

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment updated successfully.');
    }
}
