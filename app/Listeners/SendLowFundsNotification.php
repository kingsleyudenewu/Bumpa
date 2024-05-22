<?php

namespace App\Listeners;

use App\Events\WalletLowFunds;
use App\Notifications\LowFundsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowFundsNotification
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
    public function handle(WalletLowFunds $event): void
    {
        $user = $event->user;
        $user->notify(new LowFundsNotification($user));
    }
}
