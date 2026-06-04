<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductModels\CartItem;
use Laravel\Paddle\Events\TransactionCompleted;

class HandlePaddleTransactionCompleted
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
    public function handle(TransactionCompleted $event): void
    {
        // $event->payload is the full Paddle webhook payload
        $payload = $event->payload;
        $customData = $payload['data']['custom_data'] ?? [];
        $orderId = $customData['order_id'] ?? null;

        if (! $orderId) {
            return;
        }

        $order = Order::with('variants')->find($orderId);

        if (! $order) {
            return;
        }

        if ($order->status !== 'pending') {
            return;
        }

        foreach ($order->variants as $variant) {
            $variant->decrement('stock_quantity', $variant->pivot->quantity);
        }

        // Update order status
        $order->update(['status' => 'processing']);

        // Create or update the payment record
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $order->user_id,
                'amount' => $payload['data']['details']['totals']['total'] / 100,
                'method' => 'card',
                'status' => 'completed',
                'transaction_id' => $payload['data']['id'] ?? null,
            ]
        );

        // Clear the user's cart
        CartItem::forUser($order->user_id)->delete();
    }
}
