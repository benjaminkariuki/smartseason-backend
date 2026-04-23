<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FieldController;
use Illuminate\Support\Facades\Route;

// Public route
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes (will require Bearer Token)
Route::middleware('auth:sanctum')->group(function () {

Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Field Management Routes ---
    Route::get('/fields', [FieldController::class, 'index']);
    Route::post('/fields', [FieldController::class, 'store']);
    Route::patch('/fields/{field}', [FieldController::class, 'update']);
    Route::delete('/fields/{field}', [FieldController::class, 'destroy']);

    // Admin-only user management
   Route::prefix('admin')->group(function () {
        Route::get('/users/all', [\App\Http\Controllers\Api\Admin\UserController::class, 'getAllUsers']); 
        Route::get('/agents', [\App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::post('/agents', [\App\Http\Controllers\Api\Admin\UserController::class, 'store']);
        Route::patch('/agents/{user}/status', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateStatus']);
        Route::get('/agents/active', [\App\Http\Controllers\Api\Admin\UserController::class, 'getActiveAgents']);
    });

// Admin Dashboard - Now strictly protected
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/dashboard')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);
});

// Agent Dashboard - Still authenticated, no admin check needed
Route::middleware(['auth:sanctum'])->prefix('agent/dashboard')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Agent\DashboardController::class, 'index']);
});


});