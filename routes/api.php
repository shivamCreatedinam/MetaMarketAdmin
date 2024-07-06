<?php

use App\Http\Controllers\API\AuthController;
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
        Route::post('logout', [AuthController::class, "logout"]);
        // Route::get('/dashboard',[HomeController::class,"dashboard"])->name("dashboard");
        // Route::get('/user-list',[UserController::class,"userList"])->name("admin_user_list");
        // Route::get("logout",[AuthController::class,"logout"])->name("admin_logout");
    });
});
