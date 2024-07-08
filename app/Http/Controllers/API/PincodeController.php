<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pincode;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PincodeController extends Controller
{
    use ApiResponseTrait;

     /**
     * Retrieve all unique states.
     *
     * Retrieves a list of all unique states from the pincodes database table.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/get-states",
     *      tags={"Indian Pincode APIs"},
     *      summary="Retrieve all unique states",
     *      description="Retrieves a list of all unique states from the pincodes database table.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="State Fetched"),
     *              @OA\Property(property="data", type="array", @OA\Items(type="string", example="State1"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Error: Internal Server Error")
     *          )
     *      )
     * )
     */
    public function getStates()
    {
        try {
            $uniqueStates = Pincode::distinct()->orderBy('state', 'asc')->pluck('state');
            return $this->successResponse($uniqueStates, "State Fetched");
        } catch (Exception $e) {
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }


     /**
     * Retrieve unique districts based on state.
     *
     * Retrieves a list of unique districts for a given state from the pincodes database table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/get-district",
     *      tags={"Indian Pincode APIs"},
     *      summary="Retrieve unique districts based on state",
     *      description="Retrieves a list of unique districts for a given state from the pincodes database table.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"state"},
     *                  @OA\Property(property="state", type="string", example="State1")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="District Fetched"),
     *              @OA\Property(property="data", type="array", @OA\Items(type="string", example="District1"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="The state field is required.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Error: Internal Server Error")
     *          )
     *      )
     * )
     */
    public function getDistrict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }
        try {
            $uniqueDistricts = Pincode::where('state', strtoupper($request->state))
                ->distinct()
                ->orderBy('district', 'asc')
                ->pluck('district');
            return $this->successResponse($uniqueDistricts, "District Fetched");
        } catch (Exception $e) {
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }


     /**
     * Retrieve pincodes based on district.
     *
     * Retrieves a list of pincodes for a given district from the pincodes database table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Post(
     *      path="/get-pincode",
     *      tags={"Indian Pincode APIs"},
     *      summary="Retrieve pincodes based on district",
     *      description="Retrieves a list of pincodes for a given district from the pincodes database table.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"district"},
     *                  @OA\Property(property="district", type="string", example="District1")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Pincode Fetched"),
     *              @OA\Property(property="data", type="array", @OA\Items(type="string", example="123456"))
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="The district field is required.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Error: Internal Server Error")
     *          )
     *      )
     * )
     */
    public function getPincode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }
        try {
            $pincodes = Pincode::where('district', $request->district)->orderBy('pincode', 'asc')->pluck('pincode');
            return $this->successResponse($pincodes, "Pincode Fetched");
        } catch (Exception $e) {
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }


      /**
     * Retrieve all states with their districts and pincodes.
     *
     * Retrieves a nested structure containing all states, each state with its districts,
     * and each district with its corresponding pincodes from the pincodes database table.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *      path="/states-districts-pincodes",
     *      tags={"Indian Pincode APIs"},
     *      summary="Retrieve all states with districts and pincodes",
     *      description="Retrieves a nested structure containing all states, each state with its districts, and each district with its corresponding pincodes from the pincodes database table.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="state", type="string", example="State1"),
     *                  @OA\Property(
     *                      property="districts",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="district", type="string", example="District1"),
     *                          @OA\Property(property="pincodes", type="array", @OA\Items(type="string", example="123456"))
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Error: Internal Server Error")
     *          )
     *      )
     * )
     */
    public function getStatesDistrictsPincodes()
    {

        try {
            $data = [];

            $states = Pincode::distinct()->orderBy('state', 'asc')->pluck('state');

            foreach ($states as $state) {
                $districts = Pincode::where('state', $state)->distinct()->orderBy('district', 'asc')->pluck('district');

                $stateData = [];
                foreach ($districts as $district) {
                    $pincodes = Pincode::where('district', $district)->orderBy('pincode', 'asc')->pluck('pincode');

                    $stateData[] = [
                        'district' => $district,
                        'pincodes' => $pincodes
                    ];
                }

                $data[] = [
                    'state' => $state,
                    'districts' => $stateData
                ];
            }

            return $this->successResponse($data, "All Pincode List Fetched");
        } catch (Exception $e) {
            return $this->errorResponse("Error: " . $e->getMessage());
        }
    }
}
