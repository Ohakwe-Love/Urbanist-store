<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressBookController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $this->syncLegacyAddress($user);

        return view('user.dashboard.addresses', [
            'user' => $user,
            'addresses' => $user->addresses()->latest()->get(),
        ]);
    }

    public function store(UserAddressRequest $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            $address = $user->addresses()->create($this->payload($request));
            $this->applyDefaults($user, $address, $request->boolean('is_default_shipping'), $request->boolean('is_default_billing'));
        });

        return redirect()->route('addresses.index')->with('success', 'Address saved successfully.');
    }

    public function update(UserAddressRequest $request, UserAddress $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($request, $address) {
            $address->update($this->payload($request));
            $this->applyDefaults($request->user(), $address, $request->boolean('is_default_shipping'), $request->boolean('is_default_billing'));
        });

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(UserAddress $address): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($address->user_id === $user->id, 404);

        $wasDefaultShipping = $address->is_default_shipping;
        $wasDefaultBilling = $address->is_default_billing;

        $address->delete();

        $fallback = $user->addresses()->latest()->first();

        if ($fallback) {
            if ($wasDefaultShipping) {
                $user->addresses()->whereKeyNot($fallback->id)->update(['is_default_shipping' => false]);
                $fallback->update(['is_default_shipping' => true]);
            }

            if ($wasDefaultBilling) {
                $user->addresses()->whereKeyNot($fallback->id)->update(['is_default_billing' => false]);
                $fallback->update(['is_default_billing' => true]);
            }
        }

        return redirect()->route('addresses.index')->with('success', 'Address removed successfully.');
    }

    protected function payload(UserAddressRequest $request): array
    {
        return [
            'label' => $request->input('label'),
            'recipient_name' => $request->input('recipient_name'),
            'phone' => $request->input('phone'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'country' => $request->input('country'),
            'is_default_shipping' => $request->boolean('is_default_shipping'),
            'is_default_billing' => $request->boolean('is_default_billing'),
        ];
    }

    protected function applyDefaults($user, UserAddress $address, bool $defaultShipping, bool $defaultBilling): void
    {
        if ($defaultShipping || $user->addresses()->where('is_default_shipping', true)->doesntExist()) {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default_shipping' => false]);
            $address->update(['is_default_shipping' => true]);
        }

        if ($defaultBilling || $user->addresses()->where('is_default_billing', true)->doesntExist()) {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default_billing' => false]);
            $address->update(['is_default_billing' => true]);
        }
    }

    protected function syncLegacyAddress($user): void
    {
        if ($user->addresses()->exists()) {
            return;
        }

        if (!$user->address || !$user->country) {
            return;
        }

        $address = $user->addresses()->create([
            'label' => 'Primary Address',
            'recipient_name' => $user->name,
            'phone' => $user->phone,
            'address_line_1' => $user->address,
            'city' => $user->city ?? 'Unknown City',
            'state' => $user->state,
            'postal_code' => $user->postal_code,
            'country' => $user->country,
            'is_default_shipping' => true,
            'is_default_billing' => true,
        ]);

        $this->applyDefaults($user, $address, true, true);
    }
}
