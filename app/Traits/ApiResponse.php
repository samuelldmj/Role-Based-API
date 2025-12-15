<?php
// app/Traits/ApiResponse.php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'code' => $code,
            'data' => $data ?? new \stdClass(),
            'message' => $message,
        ], $code);
    }


    protected function errorResponse(
        string $message = 'Error',
        int $code = 400,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'code' => $code,
            'data' => $data ?? new \stdClass(),
            'message' => $message,
        ], $code);
    }


    protected function validationErrorResponse(
        mixed $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'code' => 422,
            'data' => $errors,
            'message' => $message,
        ], 422);
    }
}
