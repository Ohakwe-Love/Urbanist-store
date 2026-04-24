<?php

namespace App\Http\Controllers;
use App\Models\News;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());

        $query = News::query()
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%");
                });
            })
            ->latest();

        $news = $query->paginate(9)->withQueryString();

        $trendingNews = (clone $query)->first();

        return view('news.index', [
            'news' => $news,
            'trendingNews' => $trendingNews
        ]);
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();

        $relatedNews = News::where('id', '!=', $news->id)
            ->latest()
            ->take(3)
            ->get();
        
        $recentPosts = News::where('id', '!=', $news->id)->latest()->take(7)->get();


        return view('news.show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
            'recentPosts' => $recentPosts
        ]); 
    }

    public function create()
    {
        // Logic to show the form for creating a new news article
    }

    public function store(Request $request)
    {
        // Logic to store a new news article
    }

    public function edit($id)
    {
        // Logic to show the form for editing an existing news article
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing news article
    }

    public function destroy($id)
    {
        // Logic to delete a news article
    }
}
