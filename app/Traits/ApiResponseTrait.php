<?php

namespace App\Traits;


trait ApiResponseTrait
{
    protected function successResponse($data = [], $message = '', $statusCode = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse($message = '', $errors = [], $statusCode = 200)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    protected function validationErrorResponse($errors)
    {
        return $this->errorResponse('Validation Error', $errors, 200);
    }
}
