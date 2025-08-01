<?php

namespace App\Http\Controllers;
use App\Models\News;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(9);

        $trendingNews = News::latest()->first();

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


        return view('news.show', [
            'news' => $news,
            'relatedNews' => $relatedNews
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
