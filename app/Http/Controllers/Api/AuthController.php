<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // Assigning default role (user)
        $defaultRole = Role::firstWhere('slug', 'user');
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }

        $token = $user->createToken('auth_token')->accessToken;

        $responseData = $this->formatUserResponse($user, $token);

        return $this->successResponse($responseData, 'User registered successfully', JsonResponse::HTTP_CREATED);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', JsonResponse::HTTP_UNAUTHORIZED);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            return $this->errorResponse('Account is deactivated', JsonResponse::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('auth_token')->accessToken;
        $responseData = $this->formatUserResponse($user, $token);

        return $this->successResponse($responseData, 'Login successful', JsonResponse::HTTP_OK);
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function profile(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'roles' => $user->roles->pluck('slug'),
            'permissions' => collect($user->getAllPermissions())->pluck('slug'),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 'Profile retrieved successfully');
    }

    /**
     * Format the user and token data for the response.
     *
     * @param  \App\Models\User  $user
     * @param  string  $token
     * @return array
     */
    private function formatUserResponse(User $user, string $token): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'roles' => $user->roles->pluck('slug'),
                'permissions' => collect($user->getAllPermissions())->pluck('slug'),
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
