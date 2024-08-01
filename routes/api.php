<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    // App Version
    Route::get('/', function () {
        return ['Laravel' => app()->version()];
    });

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Authenticated
    Route::middleware(['auth:sanctum'])->group(function () {
        // Profile
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        // User
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/user/{user}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });
});
