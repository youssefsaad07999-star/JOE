<?php

namespace App\Listeners;

use App\Models\ProductModels\CartItem;
use Illuminate\Auth\Events\Login;

class MergeGuestCart
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // dd('hello', session('guest_session_id'));
        $sessionId = session('guest_session_id');

        $guestItems = CartItem::forSession($sessionId)->get();

        if ($guestItems->isEmpty()) {
            return;
        }

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::forUser($event->user->id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $stock = $guestItem->variant->stock_quantity;
                $merged = min($existing->quantity + $guestItem->quantity, $stock);

                $existing->update(['quantity' => $merged]);
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'user_id' => $event->user->id,
                    'session_id' => null,
                ]);
            }
        }
    }
}
