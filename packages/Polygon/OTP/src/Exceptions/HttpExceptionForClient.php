<?php

namespace Polygon\OTP\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class HttpExceptionForClient extends Exception
{
    public mixed $errors = null;

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->getCode());
    }
}
