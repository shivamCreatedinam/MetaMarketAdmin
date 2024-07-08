<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserAadharVerification;
use App\Models\UserPanCardVerification;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageUploadTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}
