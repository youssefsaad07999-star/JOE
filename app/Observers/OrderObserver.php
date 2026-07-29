<?php

namespace App\Observers;

use App\Models\Order;
use App\OrderStatus;
use App\PaymentStatus;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === OrderStatus::Refunded) {
            // Observer remains silent because the Filament Refund Action handled restocking safely
            return;
        } elseif ($order->wasChanged('status') && $order->status === OrderStatus::Delivered) {

            // 2. Check if the payment method was Cash on Delivery
            if ($order->payment->method === 'cash on delivery') {

                // 3. Automatically update or create the payment record
                $order->payment->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'amount' => $order->total_price,
                        'method' => 'cash on delivery',
                        'status' => 'paid',

                    ]
                );
            }
        } elseif ($order->wasChanged('status') && $order->status === OrderStatus::Cancelled) {

            $order->payment->update(['status' => PaymentStatus::Cancelled]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
