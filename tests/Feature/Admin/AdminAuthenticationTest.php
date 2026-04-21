<?php

use App\Models\Admin;

it('shows a dedicated admin login form', function () {
    $response = $this->get(route('admin.login'));

    $response->assertOk();
    $response->assertSee('Admin Access');
    $response->assertDontSee('Create an Account');
});

it('allows admins to log in through the admin guard', function () {
    $admin = Admin::factory()->create([
        'email' => 'boss@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post(route('admin.authenticate'), [
        'email' => $admin->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated('admin');
});
