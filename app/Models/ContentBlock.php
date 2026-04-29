<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'key',
        'title',
        'content',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            'home_banner' => [
                'title' => 'Sleeper Sofas',
                'content' => 'New arrivals with comfort and style for your living space',
                'meta' => [
                    'slide_two_title' => 'Fabric Sofas',
                    'slide_two_content' => 'Fabric sofas for stylish living rooms',
                    'slide_three_title' => 'Arm Chair',
                    'slide_three_content' => 'Create your perfect sanctuary with our exclusive collection',
                    'cta_label' => 'Shop Now',
                ],
                'is_active' => true,
            ],
            'promotional_section' => [
                'title' => 'New arrivals',
                'content' => 'Discover the latest trends and styles in home decor.',
                'meta' => [
                    'secondary_title' => 'Top trending',
                    'secondary_content' => 'Explore our top trending products that are loved by our customers.',
                ],
                'is_active' => true,
            ],
            'featured_collections' => [
                'title' => 'Featured collections',
                'content' => 'Shop our best selling collections for a range of styles loved by you.',
                'meta' => [],
                'is_active' => true,
            ],
            'about_page_content' => [
                'title' => 'Transforming Spaces Into Experiences',
                'content' => "Founded in 2020, Urbanist is more than just a furniture store. We are a lifestyle destination committed to bringing modern, sustainable design into every home.\n\nAt Urbanist, we believe that your living space should be a reflection of your personality and aspirations. That is why we collaborate with talented designers and craftsmen from around the world to create exclusive pieces that stand out and stand the test of time.\n\nOur mission goes beyond selling furniture. We aim to inspire a conscious approach to modern living, where quality, sustainability, and thoughtful design converge to create spaces that nurture well-being and connection.",
                'meta' => [
                    'services_heading' => 'Ready to Upgrade Your Living Space?',
                    'services_content' => 'Whether you are furnishing a new home, redesigning a space, or simply looking for the perfect accent piece, Urbanist offers comprehensive services to meet your needs.',
                ],
                'is_active' => true,
            ],
            'contact_details' => [
                'title' => 'Get in Touch',
                'content' => 'Please enter the details of your request. A member of our support staff will respond as soon as possible.',
                'meta' => [
                    'sidebar_heading' => 'Contact Info',
                    'sidebar_content' => 'Feel free to reach out to us. Urbanist cares.',
                ],
                'is_active' => true,
            ],
        ];
    }
}
