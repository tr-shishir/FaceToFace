<?php

namespace Polygon\OTP;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Polygon\OTP\Core\ContactInformation;
use Polygon\OTP\Core\Mobile;
use Polygon\OTP\Exceptions\InvalidOTPException;
use Polygon\OTP\Exceptions\TooManyOTPRequestException;

abstract class OTP
{
    const OTP_LENGTH = 6;

    const MAX_WRONG_TRY = 5;

    const VALIDITY_MINUTES = 2;

    const RETRY_AFTER_MINUTES = 2;

    /** @var Mobile */
    protected Mobile|ContactInformation $contactType;

    protected string $otp;

    abstract protected function shootToContact();

    public function __construct(ContactInformation $contactType, string $otp)
    {
        $this->contactType = $contactType;
        $this->otp = $otp;
    }

    public static function generate(ContactInformation $contactType): OTP
    {
        $otp = self::wantsDummyOtp($contactType) ? 0 : rand(0, pow(10, self::OTP_LENGTH) - 1);
        $otp = str_pad("$otp", self::OTP_LENGTH, '0', STR_PAD_LEFT);

        return new static($contactType, $otp);
    }

    /**
     * @throws TooManyOTPRequestException
     */
    public function shoot(): OTPShot
    {
        $retry_after = $this->checkSuccessiveHit();
        $this->save($this->otp, now()->addMinutes(self::VALIDITY_MINUTES));
        $this->shootToContact();

        return new OTPShot($this->contactType, $retry_after);
    }

    /**
     * @throws InvalidOTPException
     */
    public function validate(): AuthorizationCode
    {
        $key = $this->makeShotKey();
        if (Cache::missing($key)) {
            throw new InvalidOTPException(self::MAX_WRONG_TRY, 'Invalid OTP. Generate OTP first.');
        }

        $data = json_decode(Cache::get($key), 1);
        if ($data['otp'] !== $this->otp) {
            $wrong_try = $this->incrementWrongTry($data);
            $try_left = self::MAX_WRONG_TRY - $wrong_try;
            if ($try_left == 0) {
                Cache::delete($key);
            }
            throw new InvalidOTPException($try_left);
        }

        Cache::delete($key);

        return AuthorizationCode::generate($this->contactType);
    }

    protected static function doesNotWantDummyOtp(ContactInformation $contactType): bool
    {
        return ! self::wantsDummyOtp($contactType);
    }

    private static function wantsDummyOtp(ContactInformation $contactType): bool
    {
        return app()->environment() != 'production' || $contactType->getFull() == '+8801678242960';
    }

    private function checkSuccessiveHit(): Carbon
    {
        $key = 'otp:successive_hit:'.$this->contactType->getFull();
        if (Cache::has($key)) {
            throw new TooManyOTPRequestException(Carbon::parse(Cache::get($key)));
        }

        $retry_after = now()->addMinutes(self::RETRY_AFTER_MINUTES);
        Cache::put($key, $retry_after->toDateTimeString(), $retry_after);

        return $retry_after;
    }

    private function save(string $otp, Carbon $valid_till, $wrong_try = 0): void
    {
        $value = json_encode([
            'otp' => $otp,
            'wrong_try' => $wrong_try,
            'valid_till' => $valid_till->toDateTimeString(),
        ]);
        Cache::put($this->makeShotKey(), $value, $valid_till);
    }

    private function makeShotKey(): string
    {
        return 'otp:shot:'.$this->contactType->getFull();
    }

    private function incrementWrongTry($data = null): int
    {
        if (! $data) {
            $key = $this->makeShotKey();
            $data = json_decode(Cache::get($key), 1);
        }
        $valid_till = Carbon::parse($data['valid_till']);
        $wrong_try = $data['wrong_try'] + 1;
        $this->save($data['otp'], $valid_till, $wrong_try);

        return $wrong_try;
    }
}
