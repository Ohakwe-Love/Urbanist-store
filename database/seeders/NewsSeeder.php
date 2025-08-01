<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news = require database_path('seeders/data/news.php');

        foreach ($news as $newsCard) {
            $baseSlug = Str::slug($newsCard['title']);
            $slug = $baseSlug;
            $counter = 1;

            // Ensure slug is unique
            while (DB::table('news')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            DB::table('news')->insert([
                'title'         => $newsCard['title'],
                'slug'          => $slug,
                'description'   => $newsCard['description'],
                'news_image'     => $newsCard['news_image'],
                'date'         => $newsCard['date'],
                'read_time'    => $newsCard['read_time'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
