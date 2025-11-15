<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
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

// Product routes
Route::prefix('products')->group(function () {
    Route::post('bulk-delete', [ProductController::class, 'bulkDelete']);
    Route::patch('{product}/status', [ProductController::class, 'updateStatus']);
    Route::get('search/{search}', [ProductController::class, 'search']);
    // ADD THIS LINE: Handle POST requests with _method=PUT
    Route::post('{product}', [ProductController::class, 'update']);
});
Route::apiResource('products', ProductController::class);

// General routes
Route::get('categories', [ProductController::class, 'categories']);
Route::get('statistics', [ProductController::class, 'statistics']);