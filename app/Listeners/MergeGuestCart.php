<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class MergeGuestCart
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function handle(Login $event): void
    {
        Log::info('=== MergeGuestCart Listener Triggered ===', [
            'user_id' => $event->user->id,
            'session_id' => session()->getId()
        ]);

        try {
            $this->cartService->mergeSessionCartIntoUserCart();
            
            Log::info('Guest cart merged successfully', [
                'user_id' => $event->user->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to merge guest cart', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}