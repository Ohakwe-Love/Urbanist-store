<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentBlockController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

        Route::resource('categories', CategoryController::class)->except(['create', 'show']);
        Route::resource('tags', TagController::class)->except(['create', 'show']);
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('payments', PaymentController::class)->only(['index', 'edit', 'update']);
        Route::resource('shipments', ShipmentController::class)->only(['index', 'edit', 'update']);
        Route::resource('coupons', CouponController::class)->except(['show']);
        Route::resource('content', ContentBlockController::class)->only(['index', 'edit', 'update']);
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
    });
