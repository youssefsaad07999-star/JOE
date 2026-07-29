<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Payment;
use App\OrderStatus;
use App\PaymentStatus;
use Laravel\Paddle\Events\WebhookReceived;

class HandlePaddleFailedOrCancelled
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
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $eventType = $payload['event_type'] ?? '';

        // Only process payment failure or cancellation events
        if (! in_array($eventType, ['transaction.payment_failed', 'transaction.canceled'])) {
            return;
        }

        $customData = $payload['data']['custom_data'] ?? [];
        $orderId = $customData['order_id'] ?? null;

        if (! $orderId) {
            return;
        }

        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        if ($eventType === 'transaction.payment_failed') {
            // Handle Payment Failure
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'amount' => ($payload['data']['details']['totals']['total'] ?? 0) / 100,
                    'method' => 'card',
                    'status' => PaymentStatus::Failed,
                    'transaction_id' => $payload['data']['id'] ?? null,
                ]
            );

            // Mark order as cancelled
            $order->update(['status' => OrderStatus::Cancelled]);

        } elseif ($eventType === 'transaction.canceled') {
            // Handle Payment Cancellation
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'user_id' => $order->user_id,
                    'amount' => ($payload['data']['details']['totals']['total'] ?? 0) / 100,
                    'method' => 'card',
                    'status' => PaymentStatus::Cancelled,
                    'transaction_id' => $payload['data']['id'] ?? null,
                ]
            );

            // Mark order as cancelled
            $order->update(['status' => OrderStatus::Cancelled]);

        }
        //     elseif ($eventType === 'transaction.created') {
        //         Payment::updateOrCreate(
        //             ['order_id' => $order->id],
        //             [
        //                 'user_id' => $order->user_id,
        //                 'amount' => ($payload['data']['details']['totals']['total'] ?? 0) / 100,
        //                 'method' => 'card',
        //                 'status' => PaymentStatus::Cancelled,
        //                 'transaction_id' => $payload['data']['id'] ?? null,
        //             ]
        //         );
        //         $order->update(['status' => OrderStatus::Cancelled]);
        //     }
    }
}
