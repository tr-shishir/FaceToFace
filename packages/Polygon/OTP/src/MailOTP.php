<?php

namespace Polygon\OTP;

use Illuminate\Support\Facades\Mail;
use Polygon\OTP\Mail\RegistrationOtp;

class MailOTP extends OTP
{
    protected function shootToContact(): void
    {
        $this->shootEmail();
    }

    private function shootEmail(): void
    {
        $emailAddress = $this->contactType->getFull();
        $email = new RegistrationOtp($emailAddress, $this->otp);

        Mail::to($emailAddress)->send($email);
    }
}
