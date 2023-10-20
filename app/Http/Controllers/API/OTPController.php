<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Polygon\OTP\Requests\AuthorizationCodeRequest;
use Polygon\OTP\Requests\OTPRequest;
use Polygon\OTP\Resources\AuthorizationCode as AuthorizationCodeResource;
use Polygon\OTP\Resources\OTPShot as OTPShotResource;
use Psr\SimpleCache\InvalidArgumentException;

class OTPController extends Controller
{
    /**
     * @throws Exception
     */
    public function shoot(OTPRequest $request): JsonResponse
    {
        $otp = $request->getRegistrationWith();
        $otp_shot = $otp->shoot();

        return response()->json(new OTPShotResource($otp_shot));
    }

    /**
     * @throws Exception|InvalidArgumentException
     */
    public function checkValidity(AuthorizationCodeRequest $request): JsonResponse
    {
        $otp = $request->getOTP();
        $auth_code = $otp->validate();

        return response()->json(new AuthorizationCodeResource($auth_code));
    }
}
