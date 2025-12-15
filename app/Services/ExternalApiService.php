<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExternalApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.jsonplaceholder.base_url', 'https://jsonplaceholder.typicode.com');
        $this->timeout = config('services.jsonplaceholder.timeout', 30);
    }

    public function getUsers(): array
    {
        try {
            // Cache the response for 5 minutes to reduce API calls
            return Cache::remember('external_users', 300, function () {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/users");

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'data' => $response->json(),
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Failed to fetch data',
                    'status' => $response->status(),
                ];
            });
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('External API Connection Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Unable to connect to external API',
                'status' => JsonResponse::HTTP_SERVICE_UNAVAILABLE,
            ];
        } catch (\Exception $e) {
            Log::error('External API Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'An error occurred',
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            ];
        }
    }
}
