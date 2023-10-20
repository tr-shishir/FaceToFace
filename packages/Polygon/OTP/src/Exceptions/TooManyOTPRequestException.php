<?php

namespace Polygon\OTP\Exceptions;

use Illuminate\Support\Carbon;
use Throwable;

class TooManyOTPRequestException extends HttpExceptionForClient
{
    public function __construct(Carbon $retry_after, int $code = 429, Throwable $previous = null)
    {
        $this->errors = ['retry_after' => now()->diffInSeconds($retry_after)];
        $message = 'Too Many OTP Request. Retry after (seconds): '.now()->diffInSeconds($retry_after);
        parent::__construct($message, $code, $previous);
    }
}
