<?php

use App\Models\News;

it('lets visitors search news articles', function () {
    News::create([
        'title' => 'Urbanist Opens New Lagos Showroom',
        'news_image' => 'assets/images/blog/blog1.webp',
        'description' => 'A new showroom opens with studio consultations and curated pieces.',
        'date' => 'April 24, 2026',
        'read_time' => 4,
    ]);

    News::create([
        'title' => 'Warehouse Update',
        'news_image' => 'assets/images/blog/blog2.webp',
        'description' => 'Operations update for wholesale partners.',
        'date' => 'April 10, 2026',
        'read_time' => 3,
    ]);

    $this->get(route('news', ['search' => 'Lagos']))
        ->assertOk()
        ->assertSee('News Search Results')
        ->assertSee('Urbanist Opens New Lagos Showroom')
        ->assertDontSee('Warehouse Update');
});
