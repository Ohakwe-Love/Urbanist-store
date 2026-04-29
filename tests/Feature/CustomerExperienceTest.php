<?php

use App\Mail\ContactMessageNotificationMail;
use App\Mail\ContactMessageReplyMail;
use App\Models\Admin;
use App\Models\ContentBlock;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

function experienceProduct(array $attributes = []): Product
{
    static $sequence = 1;

    $number = $sequence++;

    return Product::create(array_merge([
        'title' => "Experience Product {$number}",
        'slug' => "experience-product-{$number}",
        'description' => 'Product used for customer experience tests.',
        'price' => 179.99,
        'category' => 'Living Room',
        'stock_quantity' => 6,
    ], $attributes));
}

it('lets a paying customer submit a product review for moderation', function () {
    $user = User::factory()->create();
    $product = experienceProduct();

    $order = Order::create([
        'order_number' => 'URB-REV-1001',
        'user_id' => $user->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'fulfillment_status' => 'delivered',
        'subtotal' => 179.99,
        'shipping_fee' => 15.00,
        'total' => 194.99,
        'currency' => 'USD',
        'email' => $user->email,
        'shipping_name' => $user->name,
        'shipping_address' => '12 Marina Road',
        'shipping_city' => 'Lagos',
        'shipping_state' => 'LA',
        'shipping_postal_code' => '100001',
        'shipping_country' => 'Nigeria',
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'product_title' => $product->title,
        'quantity' => 1,
        'unit_price' => 179.99,
        'total_price' => 179.99,
    ]);

    $this->actingAs($user)
        ->post(route('reviews.store', $product), [
            'rating' => 5,
            'title' => 'Excellent finish',
            'body' => 'The quality is strong, the finish looks premium, and the delivery experience was smooth.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
        'title' => 'Excellent finish',
        'is_approved' => false,
    ]);
});

it('does not let non-purchasers submit product reviews', function () {
    $user = User::factory()->create();
    $product = experienceProduct();

    $this->actingAs($user)
        ->post(route('reviews.store', $product), [
            'rating' => 4,
            'title' => 'Looks good',
            'body' => 'I like the look, but this should not go through because I did not buy it.',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('reviews', [
        'product_id' => $product->id,
        'user_id' => $user->id,
    ]);
});

it('shows approved reviews on product pages', function () {
    $user = User::factory()->create([
        'name' => 'Amina Bello',
    ]);
    $product = experienceProduct();

    Review::create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 5,
        'title' => 'Approved Review',
        'body' => 'This review should appear publicly on the product page once approved.',
        'is_approved' => true,
    ]);

    $this->get(route('show', $product))
        ->assertOk()
        ->assertSee('Approved Review')
        ->assertSee('Amina Bello')
        ->assertSee('Based on 1 review');
});

it('stores contact messages and newsletter subscriptions', function () {
    Mail::fake();

    $admin = Admin::factory()->create([
        'email' => 'admin-support@example.com',
    ]);

    Setting::updateOrCreate(['key' => 'contact_email'], ['value' => 'hello@urbanist-store.com']);

    $this->post(route('contact.submit'), [
        'name' => 'Ada Nwosu',
        'email' => 'ada@example.com',
        'phone' => '+2348012345678',
        'message' => 'I want to ask about delivery coverage, white-glove setup, and lead times for Lagos orders.',
    ])->assertRedirect();

    $this->post(route('newsletter.store'), [
        'email' => 'subscriber@example.com',
    ])->assertRedirect();

    expect(ContactMessage::where('email', 'ada@example.com')->exists())->toBeTrue();
    expect(NewsletterSubscriber::where('email', 'subscriber@example.com')->exists())->toBeTrue();

    Mail::assertSent(ContactMessageNotificationMail::class, 2);
    Mail::assertSent(ContactMessageNotificationMail::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email);
    });
    Mail::assertSent(ContactMessageNotificationMail::class, function ($mail) {
        return $mail->hasTo('hello@urbanist-store.com');
    });
});

it('shows only the signed-in users payments', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userOrder = Order::create([
        'order_number' => 'URB-PAY-1001',
        'user_id' => $user->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'fulfillment_status' => 'pending',
        'subtotal' => 150.00,
        'shipping_fee' => 10.00,
        'total' => 160.00,
        'currency' => 'USD',
        'email' => $user->email,
        'shipping_name' => $user->name,
    ]);

    $otherOrder = Order::create([
        'order_number' => 'URB-PAY-1002',
        'user_id' => $otherUser->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'fulfillment_status' => 'pending',
        'subtotal' => 210.00,
        'shipping_fee' => 10.00,
        'total' => 220.00,
        'currency' => 'USD',
        'email' => $otherUser->email,
        'shipping_name' => $otherUser->name,
    ]);

    Payment::create([
        'order_id' => $userOrder->id,
        'payment_reference' => 'PAY-USER-1001',
        'method' => 'paystack',
        'amount' => 160.00,
        'status' => 'successful',
        'paid_at' => now(),
    ]);

    Payment::create([
        'order_id' => $otherOrder->id,
        'payment_reference' => 'PAY-USER-1002',
        'method' => 'paystack',
        'amount' => 220.00,
        'status' => 'successful',
        'paid_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('payments.index'))
        ->assertOk()
        ->assertSee('PAY-USER-1001')
        ->assertDontSee('PAY-USER-1002');
});

it('lets an admin reply to a contact message from the support office', function () {
    Mail::fake();

    $admin = Admin::factory()->create();
    $message = ContactMessage::create([
        'name' => 'Moyo Ade',
        'email' => 'moyo@example.com',
        'phone' => '+2348099999999',
        'message' => 'Can I get an estimated delivery window for a dining set order to Abuja?',
    ]);

    $this->actingAs($admin, 'admin')
        ->post(route('admin.support.reply', $message), [
            'body' => 'Yes. Standard delivery to Abuja is usually three to five business days after payment confirmation.',
        ])
        ->assertRedirect(route('admin.support.show', $message));

    $this->assertDatabaseHas('contact_message_replies', [
        'contact_message_id' => $message->id,
        'admin_id' => $admin->id,
    ]);

    Mail::assertSent(ContactMessageReplyMail::class, function ($mail) use ($message) {
        return $mail->hasTo($message->email);
    });

    expect(ContactMessageReply::where('contact_message_id', $message->id)->exists())->toBeTrue();
});

it('orders support inbox messages by latest activity', function () {
    $admin = Admin::factory()->create();

    $olderMessage = ContactMessage::create([
        'name' => 'First Customer',
        'email' => 'first@example.com',
        'phone' => '+2348000000001',
        'message' => 'I sent the first message.',
    ]);
    $olderMessage->forceFill([
        'created_at' => Carbon::parse('2026-04-27 08:00:00'),
        'updated_at' => Carbon::parse('2026-04-27 08:00:00'),
    ])->save();

    $newerMessage = ContactMessage::create([
        'name' => 'Second Customer',
        'email' => 'second@example.com',
        'phone' => '+2348000000002',
        'message' => 'I sent the second message later.',
    ]);
    $newerMessage->forceFill([
        'created_at' => Carbon::parse('2026-04-27 09:00:00'),
        'updated_at' => Carbon::parse('2026-04-27 09:00:00'),
    ])->save();

    $reply = ContactMessageReply::create([
        'contact_message_id' => $olderMessage->id,
        'admin_id' => $admin->id,
        'body' => 'Following up on the first message.',
    ]);
    $reply->forceFill([
        'created_at' => Carbon::parse('2026-04-27 10:00:00'),
        'updated_at' => Carbon::parse('2026-04-27 10:00:00'),
    ])->save();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.support.index'))
        ->assertOk();

    $content = $response->getContent();

    expect(strpos($content, 'First Customer'))->toBeLessThan(strpos($content, 'Second Customer'));
});

it('shows support replies in chronological order', function () {
    $admin = Admin::factory()->create([
        'name' => 'Urbanist Admin',
    ]);

    $message = ContactMessage::create([
        'name' => 'Chronology Customer',
        'email' => 'chrono@example.com',
        'phone' => '+2348000000003',
        'message' => 'Original customer note.',
    ]);

    $firstReply = ContactMessageReply::create([
        'contact_message_id' => $message->id,
        'admin_id' => $admin->id,
        'body' => 'First admin reply.',
    ]);
    $firstReply->forceFill([
        'created_at' => Carbon::parse('2026-04-27 10:00:00'),
        'updated_at' => Carbon::parse('2026-04-27 10:00:00'),
    ])->save();

    $secondReply = ContactMessageReply::create([
        'contact_message_id' => $message->id,
        'admin_id' => $admin->id,
        'body' => 'Second admin reply.',
    ]);
    $secondReply->forceFill([
        'created_at' => Carbon::parse('2026-04-27 11:00:00'),
        'updated_at' => Carbon::parse('2026-04-27 11:00:00'),
    ])->save();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('admin.support.show', $message))
        ->assertOk();

    $content = $response->getContent();

    expect(strpos($content, 'First admin reply.'))->toBeLessThan(strpos($content, 'Second admin reply.'));
});

it('renders admin-managed content blocks on storefront pages', function () {
    ContentBlock::query()->updateOrCreate(
        ['key' => 'home_banner'],
        [
            'title' => 'Curated Living',
            'content' => 'Homepage copy should come from the admin content block.',
            'meta' => [
                'slide_two_title' => 'Studio Seating',
                'slide_two_content' => 'Second slide copy from content blocks.',
                'slide_three_title' => 'Accent Lounge',
                'slide_three_content' => 'Third slide copy from content blocks.',
                'cta_label' => 'Browse now',
            ],
            'is_active' => true,
        ]
    );

    ContentBlock::query()->updateOrCreate(
        ['key' => 'about_page_content'],
        [
            'title' => 'Built Around Better Rooms',
            'content' => "The about page now reads from the admin office.\n\nThat keeps merchandising copy editable.",
            'meta' => [
                'services_heading' => 'Design Support',
                'services_content' => 'Service lead copy should also be editable from admin.',
            ],
            'is_active' => true,
        ]
    );

    ContentBlock::query()->updateOrCreate(
        ['key' => 'contact_details'],
        [
            'title' => 'Talk to Urbanist',
            'content' => 'Contact form intro copy should be editable from admin.',
            'meta' => [
                'sidebar_heading' => 'Reach Us',
                'sidebar_content' => 'Sidebar copy should also come from admin content.',
            ],
            'is_active' => true,
        ]
    );

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Curated Living')
        ->assertSee('Browse now');

    $this->get(route('about'))
        ->assertOk()
        ->assertSee('Built Around Better Rooms')
        ->assertSee('Design Support');

    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Talk to Urbanist')
        ->assertSee('Reach Us');
});

it('renders storefront footer details from settings', function () {
    Setting::updateOrCreate(['key' => 'business_hours'], ['value' => 'Monday to Friday, 8am to 6pm WAT']);
    Setting::updateOrCreate(['key' => 'instagram_url'], ['value' => 'https://instagram.com/urbanist-store']);
    Setting::updateOrCreate(['key' => 'twitter_url'], ['value' => 'https://twitter.com/urbaniststore']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Monday to Friday, 8am to 6pm WAT')
        ->assertSee('https://instagram.com/urbanist-store', false)
        ->assertSee('https://twitter.com/urbaniststore', false);
});

it('shows the services page', function () {
    $this->get(route('services'))
        ->assertOk()
        ->assertSee('Design-led services for modern homes.');
});

it('shows the help center page and footer link', function () {
    $this->get(route('help-center'))
        ->assertOk()
        ->assertSee('How can we help?')
        ->assertSee('Frequently asked questions');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('help-center'), false);
});
