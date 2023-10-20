<?php

namespace Polygon\OTP;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Polygon\OTP\Core\ContactInformation;

class AuthorizationCode
{
    /** @var string */
    private mixed $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public static function generate(ContactInformation $contactType): AuthorizationCode
    {
        $code = new AuthorizationCode(Str::random(60));
        $code->save($contactType);

        return $code;
    }

    public function save(ContactInformation $contactType)
    {
        Cache::put($this->makeKey(), $contactType->getFull(), now()->addMinutes(5));
        $type = get_class($contactType);
        Cache::put($this->makeKey().'type', $type, now()->addMinutes(1));
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @throws \Exception
     */
    public function validate(): ContactInformation
    {
        $key = $this->makeKey();

        if (Cache::missing($key)) {
            throw ValidationException::withMessages(['authorization_code' => 'Invalid authorization code.']);
        }

        $contact = Cache::get($key);
        $contactType = Cache::get($key.'type');

        Cache::delete($key);
        Cache::delete($key.'type');

        return $this->contactType($contact, $contactType);
    }

    private function makeKey(): string
    {
        return 'otp:auth_code:'.$this->code;
    }

    private function contactType($contact, $type): ContactInformation
    {
        return new $type($contact);
    }

    public function getOtpIndentifierByAuthCOde($authCode = null)
    {
        $key = $this->makeKey();
        $contactType = Cache::get($key.'type');
        $contact = Cache::get($key);
        if (Cache::missing($key) || ! $contactType) {
            throw ValidationException::withMessages(['authorization_code' => 'Invalid authorization code.']);
        }

        return $this->contactType($contact, $contactType);
    }
}
