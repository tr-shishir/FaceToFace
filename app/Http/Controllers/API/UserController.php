<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\LocationsLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Polygon\OTP\AuthorizationCode;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function userLogin(LoginRequest $request)
    {
        $request->authenticate();

        $request->user()->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        $token = JWTAuth::fromUser($request->user());

        return response()->json(['token' => $token]);

    }

    public function userDetails()
    {
        $user = Auth::user()->load(['location']);

        return response()->json(['data' => $user]);
    }

    public function register(RegisterRequest $request)
    {
        $email = $request->getAuthorizationCode()->validate()->getFull();
        $exists = User::where('email', $email)->first();

        if($exists){
            return response()->json(['message' => 'User Exists'], 402);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        LocationsLog::updateOrInsert(
            [
                'user_id' => $user->id
            ],
            [
                'latitude' => $request->lat,
                'longitude' => $request->long,
            ]
        );

        event(new Registered($user));

        $credentials = ['email' => $email, 'password' => $request->password];

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
