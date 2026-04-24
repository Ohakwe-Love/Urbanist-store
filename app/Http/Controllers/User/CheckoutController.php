<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private PaystackService $paystackService,
    ) {
    }

    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty. Add products before checking out.');
        }

        $user = auth()->user();
        $defaultShippingAddress = $user->addresses()->where('is_default_shipping', true)->latest()->first();
        $summary = $this->buildSummary($cart);

        return view('user.checkout', [
            'cartItems' => $cart->items,
            'defaultShippingAddress' => $defaultShippingAddress,
            'subtotal' => $summary['subtotal'],
            'shipping' => $summary['shipping'],
            'discount' => $summary['discount'],
            'total' => $summary['total'],
            'currency' => $summary['currency'],
            'currencySymbol' => $summary['currency_symbol'],
            'paystackPublicKey' => config('services.paystack.public_key'),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if (!$this->paystackService->isConfigured()) {
            return redirect()->route('checkout')->with('error', 'Paystack is not configured yet. Add your Paystack keys first.');
        }

        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty. Add products before checking out.');
        }

        $this->assertCartInventory($cart);

        $summary = $this->buildSummary($cart);
        $shippingName = trim($request->string('first_name')->toString().' '.$request->string('last_name')->toString());
        $orderNumber = $this->generateOrderNumber();
        $reference = $this->generatePaymentReference();
        $order = null;
        $payment = null;

        DB::transaction(function () use ($request, $cart, $summary, $shippingName, $orderNumber, $reference, &$order, &$payment) {
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'fulfillment_status' => 'pending',
                'subtotal' => $summary['subtotal'],
                'discount_total' => $summary['discount'],
                'shipping_fee' => $summary['shipping'],
                'total' => $summary['total'],
                'currency' => $summary['currency'],
                'email' => $request->string('email')->toString(),
                'phone' => $request->string('phone')->toString(),
                'shipping_name' => $shippingName,
                'shipping_address' => collect([
                    $request->string('address')->toString(),
                    $request->input('address_2'),
                ])->filter()->implode(', '),
                'shipping_city' => $request->string('city')->toString(),
                'shipping_state' => $request->string('state')->toString(),
                'shipping_postal_code' => $request->string('postal_code')->toString(),
                'shipping_country' => $request->string('country')->toString(),
                'notes' => $request->input('order_notes'),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_title' => $item->product?->title ?? 'Unavailable product',
                    'sku' => $item->product?->slug ? strtoupper(str_replace('-', '', $item->product->slug)) : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                    'attributes' => [
                        'category' => $item->product?->category,
                        'size' => $item->product?->size,
                    ],
                ]);
            }

            $payment = $order->payment()->create([
                'payment_reference' => $reference,
                'gateway' => 'paystack',
                'currency' => $summary['currency'],
                'method' => 'paystack',
                'amount' => $summary['total'],
                'status' => 'pending',
                'notes' => 'Awaiting Paystack payment confirmation.',
            ]);

            $order->shipment()->create([
                'status' => 'pending',
                'shipping_fee' => $summary['shipping'],
                'notes' => 'Shipment will be prepared after successful payment.',
            ]);

            $this->saveAddressIfRequested($request);
        });

        try {
            $initialization = $this->paystackService->initializeTransaction([
                'email' => $order->email,
                'amount' => $this->toMinorUnits($order->total),
                'reference' => $payment->payment_reference,
                'currency' => $order->currency,
                'callback_url' => route('checkout.callback'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                ],
            ]);
        } catch (\Throwable $exception) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Paystack initialization failed: '.$exception->getMessage(),
            ]);

            return redirect()->route('checkout')->with('error', 'We could not start your payment right now. Please try again.');
        }

        $payment->update([
            'authorization_url' => $initialization['authorization_url'] ?? null,
            'access_code' => $initialization['access_code'] ?? null,
            'gateway_response' => $initialization,
        ]);

        return redirect()->away($initialization['authorization_url']);
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference', '');
        $payment = Payment::query()->with(['order.items.product', 'order.shipment'])->firstWhere('payment_reference', $reference);

        if (!$payment || !$payment->order) {
            return redirect()->route('orders.index')->with('error', 'We could not locate that payment record.');
        }

        try {
            $verification = $this->paystackService->verifyTransaction($reference);
            $successful = $this->synchronizePaymentState($payment, $verification);
        } catch (\Throwable $exception) {
            return redirect()->route('orders.show', $payment->order)->with('error', 'Your payment is still being confirmed. Please refresh this order shortly.');
        }

        if (!$successful) {
            return redirect()->route('orders.show', $payment->order)->with('error', 'Payment was not completed. You can try again from your order history if needed.');
        }

        if (auth()->check() && auth()->id() === $payment->order->user_id) {
            return redirect()->route('checkout.complete', $payment->order)->with('success', 'Order placed successfully.');
        }

        return redirect()->route('login')->with('success', 'Payment received. Please log in to view your order.');
    }

    public function complete(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 404);

        $order->load(['items.product', 'payment', 'shipment']);

        return view('user.checkout-complete', [
            'order' => $order,
            'currencySymbol' => $order->currency_symbol,
        ]);
    }

    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (!$this->paystackService->hasValidSignature($payload, $signature)) {
            return response('Invalid signature', 401);
        }

        $event = json_decode($payload, true);
        $reference = data_get($event, 'data.reference');

        if (($event['event'] ?? null) !== 'charge.success' || !$reference) {
            return response('Event ignored', 200);
        }

        $payment = Payment::query()->with(['order.items.product', 'order.shipment'])->firstWhere('payment_reference', $reference);

        if (!$payment) {
            return response('Payment not found', 404);
        }

        $verification = $this->paystackService->verifyTransaction($reference);
        $this->synchronizePaymentState($payment, $verification);

        return response('Webhook handled', 200);
    }

    protected function synchronizePaymentState(Payment $payment, array $verification): bool
    {
        $order = $payment->order;
        abort_if(!$order, 404);

        $gatewayStatus = strtolower((string) ($verification['status'] ?? 'pending'));
        $paidAmount = round(((float) ($verification['amount'] ?? 0)) / 100, 2);

        if ($gatewayStatus !== 'success') {
            $payment->update([
                'status' => $gatewayStatus === 'failed' ? 'failed' : 'pending',
                'verified_at' => now(),
                'gateway_response' => $verification,
                'notes' => 'Paystack returned status: '.$gatewayStatus,
            ]);

            $order->update([
                'payment_status' => $gatewayStatus === 'failed' ? 'failed' : 'pending',
            ]);

            return false;
        }

        if (abs($paidAmount - (float) $order->total) > 0.01) {
            $payment->update([
                'status' => 'disputed',
                'verified_at' => now(),
                'gateway_response' => $verification,
                'notes' => 'Verified amount did not match the order total. Manual review required.',
            ]);

            $order->update([
                'payment_status' => 'disputed',
            ]);

            return false;
        }

        DB::transaction(function () use ($payment, $order, $verification, $paidAmount) {
            if ($payment->status !== 'successful') {
                foreach ($order->items as $item) {
                    if ($item->product instanceof Product && $item->product->stock_quantity >= $item->quantity) {
                        $item->product->decrement('stock_quantity', $item->quantity);
                    }
                }

                $this->clearPurchasedCart($order);
            }

            $payment->update([
                'status' => 'successful',
                'amount' => $paidAmount,
                'paid_at' => $payment->paid_at ?? now(),
                'verified_at' => now(),
                'gateway_response' => $verification,
                'notes' => 'Paystack payment verified successfully.',
            ]);

            $order->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmed_at' => $order->confirmed_at ?? now(),
            ]);

            $order->shipment()?->update([
                'status' => 'pending',
                'notes' => 'Payment received. Shipment is awaiting fulfillment.',
            ]);
        });

        return true;
    }

    protected function buildSummary($cart): array
    {
        $subtotal = round((float) $cart->items->sum(fn ($item) => $item->price * $item->quantity), 2);
        $shipping = $subtotal === 0.0 ? 0.0 : ($subtotal >= 1000 ? 0.0 : 45.0);
        $discount = 0.0;
        $total = round($subtotal + $shipping - $discount, 2);
        $currency = $this->paystackService->currency();

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'currency' => $currency,
            'currency_symbol' => $this->currencySymbol($currency),
        ];
    }

    protected function assertCartInventory($cart): void
    {
        foreach ($cart->items as $item) {
            $product = $item->product;

            if (!$product || !$product->isVisibleOnStorefront() || $product->stock_quantity < $item->quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cart' => "Some items in your cart are no longer available in the requested quantity.",
                ]);
            }
        }
    }

    protected function saveAddressIfRequested(CheckoutRequest $request): void
    {
        if (!$request->boolean('save_address')) {
            return;
        }

        $user = $request->user();

        $address = $user->addresses()
            ->where('address_line_1', $request->string('address')->toString())
            ->where('postal_code', $request->string('postal_code')->toString())
            ->first();

        $payload = [
            'label' => $address?->label ?? 'Checkout Address',
            'recipient_name' => trim($request->string('first_name')->toString().' '.$request->string('last_name')->toString()),
            'phone' => $request->string('phone')->toString(),
            'address_line_1' => $request->string('address')->toString(),
            'address_line_2' => $request->input('address_2'),
            'city' => $request->string('city')->toString(),
            'state' => $request->string('state')->toString(),
            'postal_code' => $request->string('postal_code')->toString(),
            'country' => $request->string('country')->toString(),
            'is_default_shipping' => !$user->addresses()->where('is_default_shipping', true)->exists(),
            'is_default_billing' => !$user->addresses()->where('is_default_billing', true)->exists(),
        ];

        if ($address) {
            $address->update($payload);
        } else {
            $user->addresses()->create($payload);
        }
    }

    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'URB-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    protected function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY-URB-'.strtoupper(Str::random(10));
        } while (Payment::query()->where('payment_reference', $reference)->exists());

        return $reference;
    }

    protected function toMinorUnits(float $amount): int
    {
        return (int) round($amount * 100);
    }

    protected function clearPurchasedCart(Order $order): void
    {
        if (!$order->user_id) {
            return;
        }

        $cart = $order->user?->cart;

        if ($cart) {
            $cart->items()->delete();
        }
    }

    protected function currencySymbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'NGN' => 'N',
            'GHS' => 'GH¢',
            'ZAR' => 'R',
            default => '$',
        };
    }
}
