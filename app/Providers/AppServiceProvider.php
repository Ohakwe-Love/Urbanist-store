<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CartService;
use App\View\Composers\CartComposer;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View::composer('*', CartComposer::class);
        // View::composer(['components.cart-menu', 'components.cart-icon'], CartComposer::class);

        View::composer([
            'components.cart-icon',
            'components.cart-menu',
            'layout'
        ], CartComposer::class);

        // View::composer('components.cart-menu', CartComposer::class);
    }
}
