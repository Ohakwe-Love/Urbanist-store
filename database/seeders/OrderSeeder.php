<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstWhere('email', 'customer@urbanist.com');

        if (!$user) {
            return;
        }

        $products = Product::query()
            ->whereIn('slug', [
                'oslo-cloud-sofa',
                'porto-nightstand',
                'monarch-office-desk',
                'verde-planter-trio',
            ])
            ->get()
            ->keyBy('slug');

        if ($products->count() < 4) {
            return;
        }

        $orders = [
            [
                'order_number' => 'URB-2026-1001',
                'status' => 'delivered',
                'payment_status' => 'paid',
                'fulfillment_status' => 'delivered',
                'subtotal' => 1885.00,
                'discount_total' => 86.00,
                'shipping_fee' => 85.00,
                'total' => 1884.00,
                'currency' => 'USD',
                'email' => $user->email,
                'phone' => $user->phone,
                'shipping_name' => $user->name,
                'shipping_address' => $user->address,
                'shipping_city' => $user->city,
                'shipping_state' => $user->state,
                'shipping_postal_code' => $user->postal_code,
                'shipping_country' => $user->country,
                'notes' => 'Customer requested doorstep delivery between 10am and 2pm.',
                'confirmed_at' => now()->subDays(12),
                'shipped_at' => now()->subDays(10),
                'delivered_at' => now()->subDays(7),
                'cancelled_at' => null,
                'items' => [
                    ['slug' => 'oslo-cloud-sofa', 'quantity' => 1, 'unit_price' => 1649.00],
                    ['slug' => 'verde-planter-trio', 'quantity' => 1, 'unit_price' => 96.00],
                    ['slug' => 'porto-nightstand', 'quantity' => 1, 'unit_price' => 140.00],
                ],
                'payment' => [
                    'payment_reference' => 'PAY-URB-1001',
                    'method' => 'card',
                    'amount' => 1884.00,
                    'status' => 'successful',
                    'paid_at' => now()->subDays(12),
                    'notes' => 'Captured via Stripe.',
                ],
                'shipment' => [
                    'carrier' => 'Urbanist White Glove',
                    'tracking_number' => 'UWG-1001-NYC',
                    'status' => 'delivered',
                    'shipping_fee' => 85.00,
                    'notes' => 'Assembled in-room on delivery.',
                    'shipped_at' => now()->subDays(10),
                    'delivered_at' => now()->subDays(7),
                ],
            ],
            [
                'order_number' => 'URB-2026-1002',
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'fulfillment_status' => 'pending',
                'subtotal' => 699.00,
                'discount_total' => 0.00,
                'shipping_fee' => 35.00,
                'total' => 734.00,
                'currency' => 'USD',
                'email' => $user->email,
                'phone' => $user->phone,
                'shipping_name' => $user->name,
                'shipping_address' => $user->address,
                'shipping_city' => $user->city,
                'shipping_state' => $user->state,
                'shipping_postal_code' => $user->postal_code,
                'shipping_country' => $user->country,
                'notes' => 'Awaiting payment confirmation.',
                'confirmed_at' => null,
                'shipped_at' => null,
                'delivered_at' => null,
                'cancelled_at' => null,
                'items' => [
                    ['slug' => 'monarch-office-desk', 'quantity' => 1, 'unit_price' => 699.00],
                ],
                'payment' => [
                    'payment_reference' => 'PAY-URB-1002',
                    'method' => 'bank_transfer',
                    'amount' => 734.00,
                    'status' => 'pending',
                    'paid_at' => null,
                    'notes' => 'Awaiting transfer receipt.',
                ],
                'shipment' => [
                    'carrier' => 'FedEx Freight',
                    'tracking_number' => 'FDX-1002-DESK',
                    'status' => 'pending',
                    'shipping_fee' => 35.00,
                    'notes' => 'Shipment will be booked after payment.',
                    'shipped_at' => null,
                    'delivered_at' => null,
                ],
            ],
            [
                'order_number' => 'URB-2026-1003',
                'status' => 'shipped',
                'payment_status' => 'paid',
                'fulfillment_status' => 'shipped',
                'subtotal' => 375.00,
                'discount_total' => 20.00,
                'shipping_fee' => 18.00,
                'total' => 373.00,
                'currency' => 'USD',
                'email' => $user->email,
                'phone' => $user->phone,
                'shipping_name' => $user->name,
                'shipping_address' => $user->address,
                'shipping_city' => $user->city,
                'shipping_state' => $user->state,
                'shipping_postal_code' => $user->postal_code,
                'shipping_country' => $user->country,
                'notes' => 'Leave package with the concierge if unavailable.',
                'confirmed_at' => now()->subDays(3),
                'shipped_at' => now()->subDay(),
                'delivered_at' => null,
                'cancelled_at' => null,
                'items' => [
                    ['slug' => 'porto-nightstand', 'quantity' => 1, 'unit_price' => 235.00],
                    ['slug' => 'verde-planter-trio', 'quantity' => 2, 'unit_price' => 70.00],
                ],
                'payment' => [
                    'payment_reference' => 'PAY-URB-1003',
                    'method' => 'paypal',
                    'amount' => 373.00,
                    'status' => 'successful',
                    'paid_at' => now()->subDays(3),
                    'notes' => 'Paid through PayPal Express.',
                ],
                'shipment' => [
                    'carrier' => 'UPS',
                    'tracking_number' => 'UPS-1003-PLANT',
                    'status' => 'in_transit',
                    'shipping_fee' => 18.00,
                    'notes' => 'In regional sorting hub.',
                    'shipped_at' => now()->subDay(),
                    'delivered_at' => null,
                ],
            ],
        ];

        foreach ($orders as $payload) {
            $order = Order::updateOrCreate(
                ['order_number' => $payload['order_number']],
                collect($payload)->except(['items', 'payment', 'shipment'])->merge([
                    'user_id' => $user->id,
                ])->all()
            );

            $order->items()->delete();

            foreach ($payload['items'] as $item) {
                $product = $products[$item['slug']];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'sku' => strtoupper(str_replace('-', '', $product->slug)),
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'attributes' => [
                        'size' => $product->size,
                        'category' => $product->category,
                    ],
                ]);
            }

            Payment::updateOrCreate(
                ['payment_reference' => $payload['payment']['payment_reference']],
                array_merge($payload['payment'], ['order_id' => $order->id])
            );

            Shipment::updateOrCreate(
                ['order_id' => $order->id],
                $payload['shipment']
            );
        }

        Review::updateOrCreate(
            [
                'product_id' => $products['oslo-cloud-sofa']->id,
                'user_id' => $user->id,
                'title' => 'Looks premium and feels even better',
            ],
            [
                'rating' => 5,
                'body' => 'The sofa arrived on time, the fabric feels substantial, and the seat depth is exactly what we wanted for movie nights.',
                'is_approved' => true,
            ]
        );

        Review::updateOrCreate(
            [
                'product_id' => $products['monarch-office-desk']->id,
                'user_id' => $user->id,
                'title' => 'Beautiful finish, still testing daily workflow',
            ],
            [
                'rating' => 4,
                'body' => 'Assembly was straightforward and the surface is generous. I have submitted a question about monitor arm compatibility.',
                'is_approved' => false,
            ]
        );
    }
}
