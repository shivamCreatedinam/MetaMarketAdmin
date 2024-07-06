<?php

namespace App\Http\Controllers\API;

use App\Events\RegistrationOTPSendEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCodes;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User's name",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="User's email address",
     *                     example="john@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="User's password (min 8 characters)",
     *                     example="password123"
     *                 ),
     *                 @OA\Property(
     *                     property="confirm_password",
     *                     type="string",
     *                     format="password",
     *                     description="Confirmation of user's password",
     *                     example="password123"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration successful. OTPs sent."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|min:10|max:10|unique:users,mobile_no',
            'password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                "name" => $request->name,
                "username" => Str::slug($request->name, "_"),
                "email" => $request->email,
                "mobile_no" => $request->mobile,
                "role" => "user",
                "password" => Hash::make($request->password),
            ]);
            $email_otp = generateOTP();
            $mobile_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "mobile_otp" => $mobile_otp,
                "email_otp" => $email_otp,
                "expire_at" => $expiresAt
            ]);

            event(new RegistrationOTPSendEvent($user));

            $data = [
                "mobile_otp" => $verificationCode->mobile_otp,
                "email_otp" => $verificationCode->email_otp,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            DB::commit();
            return $this->successResponse($data, "We have sent OTP your mobile number & email. OTPs expire within 5 min.");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/verify-registration-otp",
     *     summary="Verify Registration OTP",
     *     description="Verify the registration OTPs for email and mobile, then log in the user using JWT.",
     *     operationId="verifyRegistrationOTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     description="User's email",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_otp",
     *                     type="string",
     *                     description="OTP sent to the user's mobile",
     *                     example="123456"
     *                 ),
     *                 @OA\Property(
     *                     property="email_otp",
     *                     type="string",
     *                     description="OTP sent to the user's email",
     *                     example="654321"
     *                 ),
     *                 required={"email", "mobile", "mobile_otp", "email_otp"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully and user logged in",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 description="JWT access token",
     *                 example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *             ),
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 description="Type of the token",
     *                 example="bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 description="Token expiration time in seconds",
     *                 example=3600
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Authenticated user details"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="OTP expired. Please resend OTP."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function verifyRegistrationOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'mobile' => 'required|digits:10|exists:users,mobile_no',
            'mobile_otp' => 'required|string',
            'email_otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();
            $verificationCode = VerificationCodes::where('user_id', $user->uuid)->first();

            if ($verificationCode) {
                if (now()->greaterThan($verificationCode->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->mobile_otp !== $verificationCode->mobile_otp) {
                    return $this->errorResponse("Mobile OTP invalid. Please resend OTP.");
                }
                if ($request->email_otp !== $verificationCode->email_otp) {
                    return $this->errorResponse("Email OTP invalid. Please resend OTP.");
                }

                $user->update([
                    'email_verified_at' => now(),
                    'mobile_verified_at' => now(),
                ]);
                $verificationCode->delete();

                // Generate JWT token for the user
                $token = JWTAuth::fromUser($user);
                $authenticatedUser = JWTAuth::setToken($token)->toUser();
                $data = [
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                    'access_token' => $token,
                    'user' => $authenticatedUser
                ];
                DB::commit();
                return $this->successResponse($data, "OTP verified successfully. Verification completed.");
            } else {
                DB::rollBack();
                return $this->errorResponse("Some error occurred. Please resend OTP.");
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/resend-registration-otp",
     *     summary="Resend Registration OTP",
     *     description="Resend the registration OTPs for email and mobile.",
     *     operationId="resendRegistrationOTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 required={"mobile"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="mobile_otp",
     *                 type="string",
     *                 description="OTP sent to the user's mobile",
     *                 example="123456"
     *             ),
     *             @OA\Property(
     *                 property="email_otp",
     *                 type="string",
     *                 description="OTP sent to the user's email",
     *                 example="654321"
     *             ),
     *             @OA\Property(
     *                 property="expire_at",
     *                 type="string",
     *                 description="OTP expiration time",
     *                 example="01 Jan 2024 01:23:45 PM"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Notification message",
     *                 example="We have sent OTP your registered mobile number(******7890) & email(***@example.com). OTPs expire within 5 min."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function resendRegistrationOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10|max:10|exists:users,mobile_no',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::where(["mobile_no" => $request->mobile])->first();
            $email_otp = generateOTP();
            $mobile_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "mobile_otp" => $mobile_otp,
                "email_otp" => $email_otp,
                "expire_at" => $expiresAt
            ]);

            event(new RegistrationOTPSendEvent($user));

            $data = [
                "mobile_otp" => $verificationCode->mobile_otp,
                "email_otp" => $verificationCode->email_otp,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            $emailMask = Str::mask($user->email, '*', 2, 5);
            $mobileMask = Str::mask($user->mobile_no, '*', 2, 5);
            $message = "We have sent OTP your registered mobile number({$mobileMask}) & email({$emailMask}). OTPs expire within 5 min.";
            DB::commit();
            return $this->successResponse($data, $message);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

     /**
     * @OA\Post(
     *     path="/forgot-password-otp",
     *     summary="Forgot Password OTP",
     *     description="Forgot Password send OTPs for email and mobile.",
     *     operationId="forgotPassword",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 required={"mobile"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="mobile_otp",
     *                 type="string",
     *                 description="OTP sent to the user's mobile",
     *                 example="123456"
     *             ),
     *             @OA\Property(
     *                 property="email_otp",
     *                 type="string",
     *                 description="OTP sent to the user's email",
     *                 example="654321"
     *             ),
     *             @OA\Property(
     *                 property="expire_at",
     *                 type="string",
     *                 description="OTP expiration time",
     *                 example="01 Jan 2024 01:23:45 PM"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Notification message",
     *                 example="We have sent OTP your registered mobile number(******7890) & email(***@example.com). OTPs expire within 5 min."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10|max:10|exists:users,mobile_no',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::where(["mobile_no" => $request->mobile])->first();
            $email_otp = generateOTP();
            $mobile_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "mobile_otp" => $mobile_otp,
                "email_otp" => $email_otp,
                "expire_at" => $expiresAt
            ]);

            event(new RegistrationOTPSendEvent($user));

            $data = [
                "mobile_otp" => $verificationCode->mobile_otp,
                "email_otp" => $verificationCode->email_otp,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            $emailMask = Str::mask($user->email, '*', 2, 5);
            $mobileMask = Str::mask($user->mobile_no, '*', 2, 5);
            $message = "We have sent OTP your registered mobile number({$mobileMask}) & email({$emailMask}). OTPs expire within 5 min.";
            DB::commit();
            return $this->successResponse($data, $message);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/login-otp-send",
     *     summary="Send & Resend OTP for Login. Use Both (Send OTP & Resend OTP)",
     *     description="Sends & Resend OTP to the registered mobile number for login authentication.",
     *     operationId="loginOTPSend",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provide mobile number for sending OTP.",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 required={"mobile"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="mobile_otp",
     *                 type="string",
     *                 example="123456"
     *             ),
     *             @OA\Property(
     *                 property="expire_at",
     *                 type="string",
     *                 example="01 Jul 2024 10:00:00 AM"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function loginOTPSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10|max:10|exists:users,mobile_no',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $user = User::where(["mobile_no" => $request->mobile])->first();
            if ($user->email_verified_at == null || $user->mobile_verified_at == null) {
                return $this->errorResponse("Please verify your mobile number and email address.");
            }
            $mobile_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "mobile_otp" => $mobile_otp,
                "expire_at" => $expiresAt
            ]);

            // event(new RegistrationOTPSendEvent($user));

            $data = [
                "mobile_otp" => $verificationCode->mobile_otp,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            $mobileMask = Str::mask($user->mobile_no, '*', 2, 5);
            $message = "We have sent OTP your registered mobile number({$mobileMask}). OTPs expire within 5 min.";
            DB::commit();
            return $this->successResponse($data, $message);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/verify-login-otp",
     *     summary="Verify Login OTP",
     *     description="Verifies the OTP sent to the user's mobile number for login.",
     *     operationId="verifyLoginOTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provide mobile number and OTP for verification.",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="User's mobile number",
     *                     example="1234567890"
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_otp",
     *                     type="string",
     *                     description="OTP received on mobile",
     *                     example="123456"
     *                 ),
     *                 required={"mobile", "mobile_otp"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token_type",
     *                 type="string",
     *                 example="bearer"
     *             ),
     *             @OA\Property(
     *                 property="expires_in",
     *                 type="integer",
     *                 example=3600
     *             ),
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Authenticated user details",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="john.doe@example.com"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function verifyLoginOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10|exists:users,mobile_no',
            'mobile_otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        DB::beginTransaction();

        try {
            $user = User::where('mobile_no', $request->mobile)->first();
            $verificationCode = VerificationCodes::where('user_id', $user->uuid)->first();

            if ($verificationCode) {
                if (now()->greaterThan($verificationCode->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->mobile_otp !== $verificationCode->mobile_otp) {
                    return $this->errorResponse("Mobile OTP invalid. Please resend OTP.");
                }

                $verificationCode->delete();

                // Generate JWT token for the user
                $token = JWTAuth::fromUser($user);
                $authenticatedUser = JWTAuth::setToken($token)->toUser();
                $data = [
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                    'access_token' => $token,
                    'user' => $authenticatedUser
                ];
                DB::commit();
                return $this->successResponse($data, "OTP verified successfully. Verification completed.");
            } else {
                DB::rollBack();
                return $this->errorResponse("Some error occurred. Please resend OTP.");
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     description="Logs out the authenticated user and invalidates the JWT token.",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Successfully logged out"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->errorResponse('Token not provided', 400);
            }

            JWTAuth::invalidate($token);

            return $this->successResponse(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return $this->errorResponse('Failed to logout', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/get-authenticate-user",
     *     summary="Get Authenticated User",
     *     description="Fetches the authenticated user's details.",
     *     operationId="getAuthenticateUser",
     *     tags={"Authentication"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User Fetched"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="john.doe@example.com"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Error message"
     *             )
     *         )
     *     )
     * )
     */
    public function getAuthenticateUser()
    {
        return $this->successResponse([auth()->user()], "User Fatched");
    }
}
