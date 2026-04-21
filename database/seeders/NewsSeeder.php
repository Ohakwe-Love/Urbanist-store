<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news = require database_path('seeders/data/news.php');

        News::query()
            ->where('title', 'Product title here')
            ->delete();

        foreach ($news as $newsCard) {
            $newsEntry = News::withTrashed()->updateOrCreate(
                ['slug' => $newsCard['slug'] ?? Str::slug($newsCard['title'])],
                [
                    'title' => $newsCard['title'],
                    'description' => $newsCard['description'],
                    'news_image' => $newsCard['news_image'],
                    'date' => $newsCard['date'],
                    'read_time' => $newsCard['read_time'] ?? null,
                ]
            );

            if ($newsEntry->trashed()) {
                $newsEntry->restore();
            }
        }
    }
}
