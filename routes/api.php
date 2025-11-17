<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'E-commerce API is running successfully',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString()
    ], 200);
});

// Product Routes
Route::prefix('products')->group(function () {
    Route::get('/search/{search}', [ProductController::class, 'search']);
});
Route::apiResource('products', ProductController::class);

// Order Routes
Route::prefix('orders')->group(function () {
    Route::get('/search/{search}', [OrderController::class, 'search']);
    Route::get('/status/{status}', [OrderController::class, 'getByStatus']);
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus']);
});
Route::apiResource('orders', OrderController::class);

// Customer Routes
Route::prefix('customers')->group(function () {
    Route::get('/search/{search}', [CustomerController::class, 'search']);
});
Route::apiResource('customers', CustomerController::class);

// Category Routes
Route::prefix('categories')->group(function () {
    Route::get('/search/{search}', [CategoryController::class, 'search']);
});
Route::apiResource('categories', CategoryController::class);

// Option Routes
Route::prefix('options')->group(function () {
    Route::get('/search/{search}', [OptionController::class, 'search']);
});
Route::apiResource('options', OptionController::class);

// User Routes
Route::prefix('users')->group(function () {
    Route::get('/search/{search}', [UserController::class, 'search']);
    Route::patch('/{id}/status', [UserController::class, 'updateStatus']);
});
Route::apiResource('users', UserController::class);

