<?php

use Illuminate\Http\Request;
use App\Enums\RoleTypeEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AssignPermissionToRoleController;
use App\Http\Controllers\Api\AssignRoleController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ManagerController;

Route::prefix('v1')->group(function () {
    // App Version
    Route::get('/', function () {
        return ['Laravel' => app()->version()];
    });

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Authenticated
    Route::middleware(['auth:sanctum'])->group(function () {
        // Profile
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        // Category
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/category/{category}', [CategoryController::class, 'show']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/category/{category}', [CategoryController::class, 'update']);
        Route::delete('/category/{category}', [CategoryController::class, 'destroy']);

        // Only Managers can access this routes
        Route::group(['middleware' => [
            'role:' . RoleTypeEnum::MANAGER->value
        ]], function () {
            // Authorization
            Route::post('/assign-role/{user}', AssignRoleController::class);
            Route::post('/assign-permission/{role}', AssignPermissionToRoleController::class);
            Route::post('/assign-direct-permission/{user}', AssignPermissionToRoleController::class);
            Route::get('/roles', [RoleController::class, 'index']);
            Route::get('/permissions', [PermissionController::class, 'index']);

            // User
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/filter', [UserController::class, 'filter']);
            Route::get('/user/{user}', [UserController::class, 'show']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/user/{user}', [UserController::class, 'update']);
            Route::delete('/user/{user}', [UserController::class, 'destroy']);

            Route::get('/managers', [ManagerController::class, 'index']);

            Route::get('/employees', [EmployeeController::class, 'index']);
        });

    });
});
