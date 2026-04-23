<?php

use App\Models\Admin;
use App\Models\User;

it('redirects guests to the dedicated admin login form', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});

it('does not treat a customer session as an admin session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});

it('allows authenticated admins to access the admin dashboard', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('hides storefront shopping and customer auth actions for admin sessions', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('admin.dashboard'), false);
    $response->assertSee(route('admin.logout'), false);
    $response->assertDontSee(route('wishlist'), false);
    $response->assertDontSee(route('login'), false);
    $response->assertDontSee(route('register'), false);
    $response->assertDontSee('id="cart-toggle"', false);
    $response->assertDontSee('id="cart-menu"', false);
});

it('does not allow admin sessions to open customer auth pages', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get(route('login'))
        ->assertRedirect(route('admin.dashboard'));

    $this->actingAs($admin, 'admin')
        ->get(route('register'))
        ->assertRedirect(route('admin.dashboard'));

    $this->actingAs($admin, 'admin')
        ->get(route('password.request'))
        ->assertRedirect(route('admin.dashboard'));
});

it('allows admins to log out from their admin session', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest('admin');
});
