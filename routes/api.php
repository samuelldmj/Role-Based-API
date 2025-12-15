<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ExternalApiController;


//test route
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'code' => 200,
        'data' => new \stdClass(),
        'message' => 'API is working',
    ], 200);
});

//Public Routes (No Authentication Required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


//Protected Routes (Require Authentication)
Route::middleware('auth:api')->group(function () {


    //Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);


    //User Management Routes (Protected by Permissions)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:view-users');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:create-users');

        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:view-users');

        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:edit-users');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:delete-users');

        Route::post('/{user}/assign-roles', [UserController::class, 'assignRoles'])
            ->middleware('permission:assign-roles');
    });


    //Role Management Routes (Protected by Permissions)
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:view-roles');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:create-roles');

        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('permission:view-roles');

        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:edit-roles');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:delete-roles');

        Route::post('/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])
            ->middleware('permission:assign-permissions');
    });


    //Permission Management Routes (Protected by Permissions)
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:view-permissions');

        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission:create-permissions');

        Route::get('/{permission}', [PermissionController::class, 'show'])
            ->middleware('permission:view-permissions');

        Route::put('/{permission}', [PermissionController::class, 'update'])
            ->middleware('permission:edit-permissions');

        Route::delete('/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('permission:delete-permissions');
    });


    //External API Routes
    Route::prefix('external')->group(function () {
        Route::get('/users', [ExternalApiController::class, 'getExternalUsers']);
    });
});


//Fallback Route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'code' => 404,
        'data' => new \stdClass(),
        'message' => 'Route not found',
    ], 404);
});
