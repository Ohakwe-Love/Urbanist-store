<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;



class MergeGuestCart
{
    use InteractsWithQueue;
    protected $cartService;
    /**
     * Create the event listener.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->cartService->mergeGuestCart();

        //  try {
        //     $this->cartService->mergeGuestCart();
        //     Log::info('Guest cart merged successfully for user: ' . $event->user->id);
        // } catch (\Exception $e) {
        //     Log::error('Failed to merge guest cart: ' . $e->getMessage(), [
        //         'user_id' => $event->user->id
        //     ]);
            
        //     // Optionally retry the job
        //     $this->release(30); // Releases the job back to queue after 30 seconds
        // }
    }

    /**
     * Handle a job failure.
     */
    // public function failed(\Throwable $exception): void
    // {
    //     Log::error('Cart merge job failed', [
    //         'exception' => $exception->getMessage()
    //     ]);
    // }

}
