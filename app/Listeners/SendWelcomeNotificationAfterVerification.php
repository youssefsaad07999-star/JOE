<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;

class SendWelcomeNotificationAfterVerification
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
    public function handle(Verified $event): void
    {
        /** @var User $user */
        $user = $event->user;

        // Fire the Welcome notification now!
        $user->notify(new WelcomeNotification($user));
    }
}
