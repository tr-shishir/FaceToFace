<?php

namespace Polygon\OTP\Core;

use Exception;
use Polygontech\CommonHelpers\Mobile\BDMobileFormatter;
use Polygontech\CommonHelpers\Mobile\BDMobileValidator;

class Mobile implements ContactInformation
{
    private string $mobile;

    /**
     * @throws Exception
     */
    public function __construct(string $mobile)
    {
        $this->mobile = BDMobileFormatter::format($mobile);

        if (BDMobileValidator::isInvalid($this->mobile)) {
            throw new Exception('Invalid number');
        }
    }

    public function getFull(): string
    {
        return $this->mobile;
    }

    public function getWithout88(): string
    {
        return substr($this->mobile, 3);
    }

    public function __toString(): string
    {
        return $this->getFull();
    }
}
