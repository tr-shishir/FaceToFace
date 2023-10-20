<?php

namespace Polygon\OTP;

use Xenon\LaravelBDSms\Facades\SMS;

class PhoneOtp extends OTP
{
    protected function shootToContact(): void
    {
        if (parent::doesNotWantDummyOtp($this->contactType)) {
            $this->shootSms();
        }
    }

    private function shootSms(): void
    {
        $app_signature = config('inkam.android_app_signature');
        $text = "<#> Your OTP is $this->otp. $app_signature";
        SMS::shoot($this->contactType->getWithout88(), $text);
    }
}
