<?php

namespace Polygon\OTP\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Polygon\OTP\Core\Email;
use Polygon\OTP\Core\Mobile;
use Polygon\OTP\MailOTP;
use Polygon\OTP\OTP;
use Polygon\OTP\PhoneOtp;
use Polygontech\CommonHelpers\Mobile\BDMobileValidationRule;

class AuthorizationCodeRequest extends FormRequest
{
    protected mixed $email;

    protected mixed $mobile;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $this->mobile = $this->get('mobile');
        $this->email = $this->get('email');

        return [
            'mobile' => ['sometimes', new BDMobileValidationRule()],
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'otp' => 'required',
        ];
    }

    /**
     * @throws \Exception
     */
    public function getRegistrationWith(): Mobile|Email
    {
        if ($this->input('email')) {
            return self::getEmail();
        } else {
            return self::getMobile();
        }
    }

    /**
     * @throws \Exception
     */
    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    /**
     * @throws \Exception
     */
    public function getMobile(): Mobile
    {
        return new Mobile($this->mobile);
    }

    /**
     * @throws \Exception
     */
    public function getOTP(): OTP
    {
        if ($this->input('email')) {
            return new MailOTP(self::getEmail(), $this->input('otp'));
        } else {
            return new PhoneOtp(self::getMobile(), $this->input('otp'));
        }
    }
}
