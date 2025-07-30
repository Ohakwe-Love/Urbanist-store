<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('news')->insert([
            [
                'title' => 'Product title here',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ac erat ut neque bibendum egestas sed quis justo. Integer non rhoncus diam. Nullam eget dapibus lectus, vitae condimentum sem',
                'news_image' => 'assets/images/new-arrivals/new-1.webp',
                'date' => '2023-04-22',
                'read_time' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Urbanist Launches New Collection',
                'description' => 'Discover our latest arrivals and exclusive offers for the season.',
                'news_image' => 'assets/images/new-arrivals/new-2.webp',
                'date' => '2023-05-10',
                'read_time' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Sustainable Fashion Trends',
                'description' => 'Learn about the latest trends in sustainable fashion and how Urbanist is leading the way.',
                'news_image' => 'assets/images/new-arrivals/new-3.webp',
                'date' => '2023-06-15',
                'read_time' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Urbanist Collaborates with Local Artists',
                'description' => 'Explore our collaboration with local artists to bring unique designs to our collection.',
                'news_image' => 'assets/images/new-arrivals/new-4.webp',
                'date' => '2023-07-20',
                'read_time' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Urbanist Celebrates 10 Years',
                'description' => 'Join us in celebrating a decade of Urbanist with special offers and   events.',
                'news_image' => 'assets/images/new-arrivals/new-5.webp',
                'date' => '2023-08-30',
                'read_time' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
