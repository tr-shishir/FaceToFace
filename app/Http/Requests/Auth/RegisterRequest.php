<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Polygon\OTP\AuthorizationCode;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'authorization_code' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ];
    }

    public function getAuthorizationCode(): AuthorizationCode
    {
        return new AuthorizationCode($this->input('authorization_code'));
    }


}
