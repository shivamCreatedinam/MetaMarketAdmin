<?php

namespace App\Listeners;

use App\Events\RegistrationOTPSendEvent;
use App\Mail\SendOTPMail;
use App\Models\VerificationCodes;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationOTPSendListener
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
    public function handle(RegistrationOTPSendEvent $event): void
    {
        try {
            Log::channel("api_auth")->info('SendOTPEmail Listener Triggered');
            $verificationCode = VerificationCodes::where(["user_id" => $event->user->uuid])->first();
            Log::channel("api_auth")->info('Verification Code fetched', ['verificationCode' => $verificationCode]);
            $data = [
                "name" => $event->user->name,
                "mobile_otp" => $verificationCode->mobile_otp,
                "email_otp" => $verificationCode->email_otp,
                "expire_at" => date('d M Y h:i:s A',strtotime($verificationCode->expire_at)),
            ];

            Mail::to($event->user->email)->send(new SendOTPMail($data));
            Log::channel("api_auth")->info('OTP Email Sent', ['email' => $event->user->email]);
        } catch (Exception $e) {
            Log::channel("api_auth")->error($e->getMessage());
        }
    }
}
