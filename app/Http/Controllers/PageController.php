<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // About Page
    public function about()
    {
        return view('pages.about');
    }

    // Services Page
    public function services()
    {
        return view('pages.services');
    }

    // Contact Page
    public function contact()
    {
        return view('pages.contact');
    }

    // Offer Page
    public function offer()
    {
        return view('pages.offer');
    }

    // News Page
    public function news()
    {
        return view('pages.news');
    }

    // Policies Page
    public function policies()
    {
        return view('pages.policies');
    }

    // returns Page
    public function returns()
    {
        return view('pages.returns');
    }

    // cookies Page
    public function cookies()
    {
        return view('pages.cookies');
    }

    // cookies Page
    public function howToOrder()
    {
        return view('pages.how-to-order');
    }
}