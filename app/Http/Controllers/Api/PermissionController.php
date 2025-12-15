<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    use ApiResponse;


    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return $this->successResponse(
            $permissions->map(fn(Permission $permission) => $this->formatPermissionResponse($permission, true)),
            'Permissions retrieved successfully'
        );
    }


    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
        ]);

        return $this->successResponse($this->formatPermissionResponse($permission), 'Permission created successfully', JsonResponse::HTTP_CREATED);
    }


    public function show(Permission $permission): JsonResponse
    {
        return $this->successResponse($this->formatPermissionResponse($permission, true), 'Permission retrieved successfully');
    }


    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $permission->update($request->only(['name', 'slug', 'description']));

        return $this->successResponse($this->formatPermissionResponse($permission), 'Permission updated successfully');
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return $this->successResponse(null, 'Permission deleted successfully');
    }

    /**
     * Format the permission data for the response.
     *
     * @param  \App\Models\Permission  $permission
     * @param  bool  $includeTimestamps
     * @return array
     */
    private function formatPermissionResponse(Permission $permission, bool $includeTimestamps = false): array
    {
        $data = [
            'id' => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->slug,
            'description' => $permission->description,
        ];

        if ($includeTimestamps) {
            $data['created_at'] = $permission->created_at;
            $data['updated_at'] = $permission->updated_at;
        }

        return $data;
    }
}
