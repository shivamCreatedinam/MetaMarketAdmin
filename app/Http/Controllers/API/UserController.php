<?php

namespace App\Http\Controllers\API;

use App\Events\EmailOTPEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAadharVerification;
use App\Models\UserPanCardVerification;
use App\Models\VerificationCodes;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUploadTrait;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponseTrait;
    use ImageUploadTrait;


    /**
     * @OA\Post(
     *     path="/user/aadhar-kyc-save",
     *     tags={"User Kyc"},
     *     summary="Save user's Aadhar KYC details",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="aadhar_no",
     *                     type="string",
     *                     description="Aadhar number",
     *                     example="123456789012"
     *                 ),
     *                 @OA\Property(
     *                     property="aadhar_photo_front",
     *                     type="file",
     *                     description="Front side of Aadhar card"
     *                 ),
     *                 @OA\Property(
     *                     property="aadhar_photo_back",
     *                     type="file",
     *                     description="Back side of Aadhar card"
     *                 ),
     *                 required={"aadhar_no", "aadhar_photo_front", "aadhar_photo_back"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aadhar Card details successfully submitted.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Aadhar Card details successfully submitted."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error message")
     *         )
     *     )
     * )
     */
    public function userAadharKycSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "aadhar_no" => "required|min:12|max:12",
            "aadhar_photo_front" => "required|image|mimes:png,jpg,jpeg|max:2048",
            "aadhar_photo_back" => "required|image|mimes:png,jpg,jpeg||max:2048",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $aadhar_photo_front_url = null;
            $aadhar_photo_back_url = null;
            $path = "aadhar_cards/" . auth()->user()->uuid;
            if ($request->hasFile("aadhar_photo_front")) {
                $aadhar_photo_front_url = $this->uploadImage($request->file('aadhar_photo_front'), $path, "aadhar_front");
            }
            if ($request->hasFile("aadhar_photo_back")) {
                $aadhar_photo_back_url = $this->uploadImage($request->file('aadhar_photo_back'), $path, "aadhar_back");
            }

            UserAadharVerification::updateOrCreate([
                "user_id" => auth()->user()->uuid,
            ], [
                "aadhar_no" => $request->input('aadhar_no'),
                "aadhar_photo_front" => $aadhar_photo_front_url,
                "aadhar_photo_back" => $aadhar_photo_back_url,
            ]);
            return $this->successResponse([], "Aadhar Card details successfully submitted.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/user/pan-kyc-save",
     *     tags={"User Kyc"},
     *     summary="Save user's PAN KYC details",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="pan_no",
     *                     type="string",
     *                     description="PAN number",
     *                     example="ABCDE1234F"
     *                 ),
     *                 @OA\Property(
     *                     property="pan_image",
     *                     type="file",
     *                     description="Image of PAN card"
     *                 ),
     *                 required={"pan_no", "pan_image"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PAN Card details successfully submitted.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="PAN Card details successfully submitted."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error message")
     *         )
     *     )
     * )
     */
    public function userPanKycSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "pan_no" => "required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/",
            "pan_image" => "required|image|mimes:png,jpg,jpeg|max:2048",
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $pan_image_url = null;
            $path = "pan_cards/" . auth()->user()->uuid;
            if ($request->hasFile("pan_image")) {
                $pan_image_url = $this->uploadImage($request->file('pan_image'), $path, "pan");
            }

            UserPanCardVerification::updateOrCreate([
                "user_id" => auth()->user()->uuid,
            ], [
                "pan_no" => $request->input('pan_no'),
                "pan_image" => $pan_image_url,
            ]);
            return $this->successResponse([], "PAN Card details successfully submitted.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/update-profile",
     *     tags={"User Profile"},
     *     summary="Update User Profile name & profile image",
     *     description="Update the authenticated user's profile information.",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="profile_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Profile successfully updated.",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => 'string',
            "profile_image" => "file|mimes:png,jpg,jpeg|max:2048"
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $user = Auth::user();
            $name = $user->name;
            if ($request->has("name") && !is_null($request->has("name"))) {
                $name = $request->name;
            }

            $profile_image = $user->profile_image;
            $path = "profile_image/" . $user->uuid;
            if ($request->hasFile("profile_image")) {
                if (!is_null($user->profile_image)) {
                    $this->deleteImage($user->profile_image);
                }
                $profile_image = $this->uploadImage($request->file('profile_image'), $path);
            }
            $user->update([
                "name" => $name,
                "profile_image" => $profile_image
            ]);
            return $this->successResponse([], "User Profile successfully update.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/send-mobile-otp",
     *     tags={"User Profile"},
     *     summary="Send mobile OTP",
     *     description="Send OTP to user's mobile number and store it with expiration time",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function sendMobileOTP(Request $request)
    {
        try {
            $user = Auth::user();
            $mobile_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $temp_token =  bin2hex(openssl_random_pseudo_bytes(16));
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "mobile_otp" => $mobile_otp,
                "email_otp" => null,
                "expire_at" => $expiresAt,
                "token" => $temp_token
            ]);

            // event(new sendMobileOTP($user));
            $mobile_no = $user->mobile_no;
            if ($request->has('new_mobile') && !is_null($request->new_mobile)) {
                $mobile_no = $request->new_mobile;
            }

            $data = [
                "temp_token" => $temp_token,
                "mobile_otp" => $verificationCode->mobile_otp,
                "mobile_no"=>$mobile_no,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            $mobileMask = Str::mask($user->mobile_no, '*', 2, 5);
            $message = "We have sent OTP your registered mobile number({$mobileMask}). OTPs expire within 5 min.";
            return $this->successResponse($data, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/send-email-otp",
     *     tags={"User Profile"},
     *     summary="Send email OTP",
     *     description="Send OTP to user's email and store it with expiration time",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function sendEmailOTP(Request $request)
    {
        try {
            $user = Auth::user();
            $email_otp = generateOTP();
            $expiresAt = now()->addMinutes(5);
            $temp_token =  bin2hex(openssl_random_pseudo_bytes(16));
            $verificationCode = VerificationCodes::updateOrCreate(["user_id" => $user->uuid], [
                "email_otp" => $email_otp,
                "mobile_otp" => null,
                "expire_at" => $expiresAt,
                "token" => $temp_token
            ]);
            $email = $user->email;
            if ($request->has('new_email') && !is_null($request->new_email)) {
                $email = $request->new_email;
            }


            event(new EmailOTPEvent($user, $email));

            $data = [
                "temp_token" => $temp_token,
                "email" => $email,
                "email_otp" => $verificationCode->email_otp,
                "expire_at" => $expiresAt->format('d M Y h:i:s A'),
            ];
            $emailMask = Str::mask($email, '*', 2, 5);
            $message = "We have sent OTP your registered email({$emailMask}). OTPs expire within 5 min.";
            return $this->successResponse($data, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/verify-old-email-otp-and-send-new-mail-otp",
     *     tags={"User Profile"},
     *     summary="Verify email OTP and send new mail OTP",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="temp_token",
     *                     type="string",
     *                     description="Temporary token"
     *                 ),
     *                 @OA\Property(
     *                     property="otp",
     *                     type="string",
     *                     description="OTP to verify"
     *                 ),
     *                 @OA\Property(
     *                     property="new_email",
     *                     type="string",
     *                     format="email",
     *                     description="New email address"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified and new email OTP sent",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="OTP expired or invalid",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     )
     * )
     */
    public function verifyEmailOTPAndSendNewMailOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "temp_token" => 'required|string|exists:verification_codes,token',
            "otp" => 'required|string',
            "new_email" => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(auth()->user()->uuid, 'uuid')
            ]
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $getOTP = VerificationCodes::where("token", $request->temp_token)->first();
            if ($getOTP) {
                if (now()->greaterThan($getOTP->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->otp !== $getOTP->email_otp) {
                    return $this->errorResponse("Email OTP invalid. Please resend OTP.");
                }
                $getOTP->delete();
                return $this->sendEmailOTP($request);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    /**
     * @OA\Post(
     *     path="/verify-new-email-otp-and-update-mail",
     *     tags={"User Profile"},
     *     summary="Verify new email OTP AND Update Mail Address",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"temp_token", "email", "otp"},
     *                 @OA\Property(property="temp_token", type="string", example="abc123token"),
     *                 @OA\Property(property="email", type="string", format="email", example="put new email again"),
     *                 @OA\Property(property="otp", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Done"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     )
     * )
     */
    public function verifyNewMailOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "temp_token" => 'required|string|exists:verification_codes,token',
            "email" => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(auth()->user()->uuid, 'uuid')
            ],
            "otp" => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $getOTP = VerificationCodes::where("token", $request->temp_token)->first();
            if ($getOTP) {
                if (now()->greaterThan($getOTP->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->otp !== $getOTP->email_otp) {
                    return $this->errorResponse("Email OTP invalid. Please resend OTP.");
                }

                $user = auth()->user();
                if ($user) {
                    $user->update([
                        "email" => $request->email
                    ]);
                    $getOTP->delete();
                    return $this->successResponse($user, "Email successfully updated. Please use new email for login.");
                } else {
                    return $this->errorResponse("Something went wrong.");
                }

            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

/**
     * @OA\Post(
     *     path="/verify-old-mobile-otp-and-send-new-mobile-otp",
     *     tags={"User Profile"},
     *     summary="Verify mobile OTP and send new mail OTP",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="temp_token",
     *                     type="string",
     *                     description="Temporary token"
     *                 ),
     *                 @OA\Property(
     *                     property="otp",
     *                     type="string",
     *                     description="OTP to verify"
     *                 ),
     *                 @OA\Property(
     *                     property="new_mobile",
     *                     type="string",
     *                     format="string",
     *                     description="New mobile Number"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified and new mobile OTP sent",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="OTP expired or invalid",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     )
     * )
     */
    public function verifyMobileOTPAndSendNewMobileOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "temp_token" => 'required|string|exists:verification_codes,token',
            "otp" => 'required|string',
            "new_mobile" => [
                'required',
                Rule::unique('users', 'mobile_no')->ignore(auth()->user()->uuid, 'uuid')
            ]
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $getOTP = VerificationCodes::where("token", $request->temp_token)->first();
            if ($getOTP) {
                if (now()->greaterThan($getOTP->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->otp !== $getOTP->mobile_otp) {
                    return $this->errorResponse("Mobile OTP invalid. Please resend OTP.");
                }
                $getOTP->delete();
                return $this->sendMobileOTP($request);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


       /**
     * @OA\Post(
     *     path="/verify-new-mobile-otp-and-update-mobile",
     *     tags={"User Profile"},
     *     summary="Verify new mobile OTP AND Update Mobile Number",
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"temp_token", "mobile", "otp"},
     *                 @OA\Property(property="temp_token", type="string", example="abc123token"),
     *                 @OA\Property(property="mobile", type="string", example="put new mobile number again"),
     *                 @OA\Property(property="otp", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Done"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *     )
     * )
     */
    public function verifyNewMobileOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "temp_token" => 'required|string|exists:verification_codes,token',
            "mobile" => [
                'required',
                Rule::unique('users', 'mobile_no')->ignore(auth()->user()->uuid, 'uuid')
            ],
            "otp" => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        try {
            $getOTP = VerificationCodes::where("token", $request->temp_token)->first();
            if ($getOTP) {
                if (now()->greaterThan($getOTP->expire_at)) {
                    return $this->errorResponse("OTP expired. Please resend OTP.");
                }
                if ($request->otp !== $getOTP->mobile_otp) {
                    return $this->errorResponse("Mobile OTP invalid. Please resend OTP.");
                }

                $user = auth()->user();
                if ($user) {
                    $user->update([
                        "mobile_no" => $request->mobile
                    ]);
                    $getOTP->delete();
                    return $this->successResponse($user, "Mobile number successfully updated.");
                } else {
                    return $this->errorResponse("Something went wrong.");
                }

            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
