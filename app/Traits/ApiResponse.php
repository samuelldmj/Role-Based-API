<?php
// app/Traits/ApiResponse.php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = JsonResponse::HTTP_OK
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
        int $code = JsonResponse::HTTP_BAD_REQUEST,
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
            'code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'data' => $errors,
            'message' => $message,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
