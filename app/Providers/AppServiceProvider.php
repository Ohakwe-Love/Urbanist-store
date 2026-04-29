<?php

namespace App\Providers;

use App\Models\ContentBlock;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use App\Services\CartService;
use App\View\Composers\CartComposer;
use Illuminate\Support\Facades\Schema;
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
        View::composer('*', CartComposer::class);
        View::composer('*', function ($view) {
            $storeSettings = Setting::defaults();

            if (Schema::hasTable('settings')) {
                $storeSettings = array_merge(
                    $storeSettings,
                    Setting::query()->pluck('value', 'key')->toArray()
                );
            }

            $contentBlocks = ContentBlock::defaults();

            if (Schema::hasTable('content_blocks')) {
                foreach (ContentBlock::query()->get() as $block) {
                    $contentBlocks[$block->key] = array_merge(
                        $contentBlocks[$block->key] ?? ['meta' => []],
                        [
                            'title' => $block->title,
                            'content' => $block->content,
                            'meta' => is_array($block->meta) ? $block->meta : [],
                            'is_active' => $block->is_active,
                        ]
                    );
                }
            }

            $view->with('storeSettings', $storeSettings);
            $view->with('contentBlocks', $contentBlocks);
        });
    }
}
