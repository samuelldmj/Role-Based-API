<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return $this->errorResponse('Unauthenticated', JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($request->user()->hasAnyRole($roles)) {
            return $next($request);
        }

        return $this->errorResponse(
            'You do not have the required role to perform this action',
            JsonResponse::HTTP_FORBIDDEN
        );
    }
}
