<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    // Home Page
    public function index()
    {
        $products = Product::visibleOnStorefront()->latest()->limit(8)->get();

        return view('pages.index')->with('latestNewProducts', $products);
    }
}
