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
    public function index(): View
    {
        return view('admin.shipments.index', [
            'shipments' => Shipment::with('order')->latest()->paginate(12),
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
