<?php

namespace Polygon\OTP\Core;

use Polygontech\CommonHelpers\Email\Email as PloygonEmail;

class Email implements ContactInformation
{
    private string $email;

    public function __construct(string $email)
    {
        $validEmail = new PloygonEmail($email);
        $this->email = $validEmail->toString();
    }

    public function getFull(): string
    {
        return $this->email;
    }
}
