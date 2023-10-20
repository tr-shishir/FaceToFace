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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function userLogin(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);
        LocationsLog::updateOrInsert(
            [
                'user_id ' => $request->user()->id,
            ],
            [
                'latitude ' => $request->lat,
                'longitude ' => $request->long,
            ]
        );

        $token = $request->user()->createToken($request->email)->accessToken;

        return response()->json(['token' => $token]);

    }

    public function userDetails()
    {
        $user = Auth::guard('api')->user()->load(['location']);

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

        Auth::login($user);
        $token = Auth::user()->createToken($user->email)->accessToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }
}
