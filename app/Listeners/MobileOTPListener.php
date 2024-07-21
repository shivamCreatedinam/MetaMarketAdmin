<?php

namespace App\Listeners;

use App\Events\MobileOTPEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MobileOTPListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(MobileOTPEvent $event): void
    {
        //
    }
}
