<?php

namespace Polygon\OTP\Exceptions;

use Throwable;

class InvalidOTPException extends HttpExceptionForClient
{
    public function __construct($try_left, string $message = '', int $code = 422, Throwable $previous = null)
    {
        $this->errors = $message ? ['otp' => $message] : ['try_left' => $try_left];
        $message = $message ?: 'Invalid OTP. Try left: '.$try_left;
        parent::__construct($message, $code, $previous);
    }
}
