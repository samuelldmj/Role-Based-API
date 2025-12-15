<?php
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

class UserController extends Controller
{
    use ApiResponse;


    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $paginator = User::with('roles')->paginate($perPage);

        return $this->successResponse([
            'users' => $paginator->map(fn(User $user) => $this->formatUserResponse($user, true)),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], 'Users retrieved successfully');
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => $request->is_active ?? true,
        ]);

        // Assign roles if provided
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            // Assign default user role
            $defaultRole = Role::where('slug', 'user')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        }

        $user->load('roles.permissions');

        return $this->successResponse($this->formatUserResponse($user), 'User created successfully', JsonResponse::HTTP_CREATED);
    }


    public function show(User $user): JsonResponse
    {
        $user->load('roles.permissions');
        return $this->successResponse($this->formatUserResponse($user, true), 'User retrieved successfully');
    }


    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $updateData = $request->only(['name', 'email', 'phone', 'is_active']);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        $user->load('roles.permissions');

        return $this->successResponse($this->formatUserResponse($user), 'User updated successfully');
    }


    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if (Auth::id() === $user->id) {
            return $this->errorResponse('Cannot delete your own account', JsonResponse::HTTP_FORBIDDEN);
        }

        // Prevent deletion of super-admin
        if ($user->hasRole('super-admin')) {
            return $this->errorResponse('Cannot delete super admin', JsonResponse::HTTP_FORBIDDEN);
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user->roles()->sync($request->roles);
        $user->load('roles.permissions');

        return $this->successResponse($this->formatUserResponse($user), 'Roles assigned successfully');
    }

    /**
     * Format the user data for the response.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $includeTimestamps
     * @return array
     */
    private function formatUserResponse(User $user, bool $includeTimestamps = false): array
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_active' => $user->is_active,
            'roles' => $user->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
            ]),
            'permissions' => collect($user->getAllPermissions())->pluck('slug'),
        ];

        if ($includeTimestamps) {
            $data['created_at'] = $user->created_at;
            $data['updated_at'] = $user->updated_at;
        }

        return $data;
    }
}
