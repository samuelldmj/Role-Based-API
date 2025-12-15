<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExternalApiService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ExternalApiController extends Controller
{
    use ApiResponse;
    public function __construct(protected ExternalApiService $externalApiService)
    {

    }

    public function getExternalUsers(): JsonResponse
    {
        $result = $this->externalApiService->getUsers();

        if ($result['success']) {
            return $this->successResponse(
                data: $result['data'],
                message: 'External users retrieved successfully',
                code: JsonResponse::HTTP_OK
            );
        }

        return $this->errorResponse(
            message: $result['error'],
            code: $result['status'],
        );
    }
}
