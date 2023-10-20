<?php

namespace Polygon\OTP;

use Illuminate\Support\Carbon;
use Polygon\OTP\Core\ContactInformation;

class OTPShot
{
    public ContactInformation $contactType;

    public Carbon $retry_after;

    public function __construct(ContactInformation $contactType, Carbon $retry_after)
    {
        $this->contactType = $contactType;
        $this->retry_after = $retry_after;
    }
}
