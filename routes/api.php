<?php

use App\Http\Controllers\Api\UserController;

use Illuminate\Support\Facades\Route;

// Health check route
Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});

// User routes
Route::prefix('users')->group(function () {
    Route::get('search/{search}', [UserController::class, 'search']);
    Route::patch('{id}/status', [UserController::class, 'updateStatus']);
});
Route::apiResource('users', UserController::class);

