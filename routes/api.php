<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PincodeController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'v1'], function () {

    Route::post("register", [AuthController::class, "register"]);
    Route::post("verify-registration-otp", [AuthController::class, "verifyRegistrationOTP"]);
    Route::post("resend-registration-otp", [AuthController::class, "resendRegistrationOTP"]);
    Route::post("login-otp-send", [AuthController::class, "loginOTPSend"]);
    Route::post("verify-login-otp", [AuthController::class, "verifyLoginOTP"]);
    Route::post("forgot-password", [AuthController::class, "forgotPassword"]);
    Route::post("verify-forgot-password-otp", [AuthController::class, "verifyForgotPasswordOTP"]);
    Route::post("update-password", [AuthController::class, "updatePassword"]);

    // Login Using Email
    Route::post("login-using-email", [AuthController::class, "loginUsingEmail"]);

    Route::group(['middleware' => ['jwt']], function () {
        Route::get("get-authenticate-user", [AuthController::class, "getAuthenticateUser"]);
        Route::post("user/aadhar-kyc-save", [UserController::class, "userAadharKycSave"]);
        Route::post("user/pan-kyc-save", [UserController::class, "userPanKycSave"]);






        Route::post('logout', [AuthController::class, "logout"]);
    });


    // Pincodes APIs
    Route::get("get-states", [PincodeController::class, "getStates"]);
    Route::post("get-district", [PincodeController::class, "getDistrict"]);
    Route::post("get-pincode", [PincodeController::class, "getPincode"]);
    Route::get('/states-districts-pincodes', [PincodeController::class, 'getStatesDistrictsPincodes']);
});
