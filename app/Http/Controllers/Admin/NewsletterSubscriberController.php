<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $subscribers = NewsletterSubscriber::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(16)
            ->withQueryString();

        return view('admin.newsletters.index', compact('subscribers'));
    }
}
