<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    use ApiResponse;


    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->get();

        return $this->successResponse(
            $roles->map(fn(Role $role) => $this->formatRoleResponse($role, true)),
            'Roles retrieved successfully',
            JsonResponse::HTTP_OK
        );
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return $this->successResponse($this->formatRoleResponse($role), 'Role created successfully', JsonResponse::HTTP_CREATED);
    }


    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');
        return $this->successResponse($this->formatRoleResponse($role, true), 'Role retrieved successfully');
    }


    public function update(Request $request, Role $role): JsonResponse
    {
        // Prevent updates to protected roles
        if (in_array($role->slug, ['super-admin', 'admin'])) {
            return $this->errorResponse('Cannot update protected role', JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$role) {
            return $this->errorResponse('Role not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $updated = $role->update($request->only(['name', 'slug', 'description']));

        if ($updated && $request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        $role->load('permissions');

        return $this->successResponse($this->formatRoleResponse($role), 'Role updated successfully', JsonResponse::HTTP_OK);
    }

    public function destroy(Role $role): JsonResponse
    {
        // Prevent deletion of protected roles
        if (in_array($role->slug, ['super-admin', 'admin'])) {
            return $this->errorResponse('Cannot delete protected role', JsonResponse::HTTP_FORBIDDEN);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }

    public function assignPermissions(Request $request, Role $role): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $role->permissions()->sync($request->permissions);
        $role->load('permissions');

        return $this->successResponse($this->formatRoleResponse($role), 'Permissions assigned successfully', JsonResponse::HTTP_OK);
    }

    /**
     * Format the role data for the response.
     *
     * @param  \App\Models\Role  $role
     * @param  bool  $includeTimestamps
     * @return array
     */
    private function formatRoleResponse(Role $role, bool $includeTimestamps = false): array
    {
        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'permissions' => $role->permissions->pluck('slug'),
        ];

        if ($includeTimestamps) {
            $data['created_at'] = $role->created_at;
            $data['updated_at'] = $role->updated_at;
        }

        return $data;
    }
}
