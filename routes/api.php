<?php

use App\Http\Controllers\API\LocationsLogController;
use App\Http\Controllers\API\OTPController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('otp/shoot', [OTPController::class, 'shoot']);
Route::post('otp/validate', [OTPController::class, 'checkValidity']);
Route::post('login',[UserController::class,'userLogin']);
Route::post('register',[UserController::class,'register']);

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');


Route::group(['middleware' => 'jwtAuth'], function (){
    Route::post('logout',[UserController::class,'logout']);
    Route::get('profile-details',[UserController::class,'userDetails']);
    Route::put('update-profile',[UserController::class,'updateProfile']);

    Route::get('locations',[LocationsLogController::class,'getLocations']);


    Route::post('update-location',[LocationsLogController::class,'updateLocation']);
});
