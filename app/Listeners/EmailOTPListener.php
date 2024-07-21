<?php

namespace App\Listeners;

use App\Events\EmailOTPEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailOTPListener
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
    public function handle(EmailOTPEvent $event): void
    {
        //
    }
}
