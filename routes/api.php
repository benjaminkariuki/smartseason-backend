<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FieldController;
use Illuminate\Support\Facades\Route;

// Public route
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (will require Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Field Management Routes ---
    Route::get('/fields', [FieldController::class, 'index']);
    Route::post('/fields', [FieldController::class, 'store']);
    Route::patch('/fields/{field}', [FieldController::class, 'update']);
    Route::delete('/fields/{field}', [FieldController::class, 'destroy']);

    // Admin-only user management
   Route::prefix('admin')->group(function () {
        Route::get('/agents', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::post('/agents', [\App\Http\Controllers\Api\Admin\UserController::class, 'store']);
        Route::patch('/agents/{user}/status', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateStatus']);
    });
    // We will add Field management routes here in the NEXT step
});