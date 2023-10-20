<?php

namespace Polygon\OTP\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Polygon\OTP\Core\Email;
use Polygon\OTP\Core\Mobile;
use Polygon\OTP\MailOTP;
use Polygon\OTP\OTP;
use Polygon\OTP\PhoneOtp;
use Polygontech\CommonHelpers\Mobile\BDMobileValidationRule;

class OTPRequest extends FormRequest
{
    protected mixed $mobile;

    protected mixed $email;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $this->mobile = $this->get('mobile');
        $this->email = $this->get('email');

        return [
            'mobile' => ['sometimes', new BDMobileValidationRule()],
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
        ];
    }

    /**
     * @throws \Exception
     */
    public function getRegistrationWith(): OTP
    {
        if ($this->email) {
            return MailOTP::generate($this->getEmail());
        } elseif ($this->mobile) {
            return PhoneOtp::generate($this->getMobile());
        }
    }

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
}
