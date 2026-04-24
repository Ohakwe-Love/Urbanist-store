<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\AddressBookController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\OrderHistoryController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\NewsController;

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
Route::get('/news', [NewsController::class, 'index'])->name('news');

// news detail page route
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');

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
Route::middleware('guest.customer')->group(function () {

    // Register and login routes
    Route::controller(AuthController::class)
    ->prefix('register')
    ->group(function(){
        Route::get('/', 'register')->name('register');
        Route::get('/verify', 'showRegistrationCodeForm')->name('register.verify');
        Route::get('/details', 'showRegistrationDetailsForm')->name('register.details');
        Route::post('/email', 'sendRegistrationVerificationEmail')->name('register.email');
        Route::post('/verify-code', 'verifyRegistrationCode')->name('register.verify-code');
        Route::post('/change-email', 'resetRegistrationVerification')->name('register.change-email');
        Route::post('/', 'store')->name('register.store');
    });

    Route::controller(AuthController::class)
    ->prefix('login')
    ->group(function(){
        
        Route::get('/', 'login')->name('login');

        Route::post('/', 'authenticate')->name('login.authenticate');
    });

    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/checkout/paystack/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::post('/payments/paystack/webhook', [CheckoutController::class, 'webhook'])->name('payments.paystack.webhook');

// Authenticated user routes
Route::middleware('customer')->prefix('user')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');
    Route::get('/addresses', [AddressBookController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressBookController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressBookController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressBookController::class, 'destroy'])->name('addresses.destroy');

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
    
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('updateProfile');
    
    // Checkout routes would go here
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/complete/{order}', [CheckoutController::class, 'complete'])->name('checkout.complete');
    
    // Route::prefix('checkout')->name('checkout.')->group(function () {

        // Route::post('/method', [CheckoutController::class, 'checkoutMethod'])->name('method');
    // });
});

// Cart API routes
Route::prefix('cart')->name('cart.')->group(function () {
    // cart summary
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');

    // add to cart
    Route::post('/add', [CartController::class, 'add'])->name('add');
    
    // update cart
    Route::patch('/update', [CartController::class, 'update'])->name('update');

    // delete from cart
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');

    // clear cart
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});
