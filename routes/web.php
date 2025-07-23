<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\ProfileController;

// Public routes

// Home page route
Route::get('/', [HomeController::class, 'index'])->name('home');

// about page route
Route::get('/about', [PageController::class, 'about'])->name('about');

// services page route
Route::get('/services', [PageController::class, 'services'])->name('services');

// contact page route
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// offer page route
Route::get('/offer', [PageController::class, 'offer'])->name('offer');

// news page route
Route::get('/news', [PageController::class, 'news'])->name('news');

// policies page route
Route::get('/policies', [PageController::class, 'policies'])->name('policies');

// Returns page route
Route::get('/returns', [PageController::class, 'returns'])->name('returns');

// Cookies page route
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');

// how-to-order page route
Route::get('/how-to-order', [PageController::class, 'howToOrder'])->name('how-to-order');

// shop page route
Route::get('/shop', [ProductController::class, 'index'])->name('shop');

// Filter products by category
Route::post('/shop/load-more', [ProductController::class, 'loadMore'])->name('shop.loadMore');

// Product detail page route
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('show');

// guest routes for authentication
Route::middleware('guest')->group(function () {

    // Register and login routes
    Route::controller(AuthController::class)
    ->prefix('register')
    ->group(function(){
        Route::get('/', 'register')->name('register');

        Route::post('/', 'store')->name('register.store');
    });

    Route::controller(AuthController::class)
    ->prefix('login')
    ->group(function(){
        
        Route::get('/', 'login')->name('login');

        Route::post('/', 'authenticate')->name('login.authenticate');
    });
});

// logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated user routes
Route::middleware('auth')->prefix('user')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist'); 
    
    // Toggle wishlist item
    Route::middleware('web')->group(function () {
        Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('throttle:60,1');
    });

    // Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    // Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
    // Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::get('/profile/edit', [ProfileController::class, 'showProfileEditForm'])->name('profileEdit');
});


// Cart routes

Route::prefix('cart')->name('cart.')->group(function () {
    // add to cart 
    Route::post('/add', [CartController::class, 'add'])->name('add');

    // update cart
    Route::patch('/update', [CartController::class, 'update'])->name('update');

    // remove from cart
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');

    // clear cart 
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');

    // summary
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
});