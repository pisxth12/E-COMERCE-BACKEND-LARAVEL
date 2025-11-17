<?php

use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Health check route
Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});

// Product routes
Route::prefix('products')->group(function(){
    Route::get('/search/{search}', [ProductController::class, 'search']);
    Route::get('/category/{category}', [ProductController::class, 'filterByCategory']);
});
Route::apiResource('products', ProductController::class);

// Order routes
Route::prefix('orders')->group(function () {
    Route::get('search/{search}', [OrderController::class, 'search']);
    Route::patch('{id}/status', [OrderController::class, 'updateStatus']);
});

// Category routes
Route::prefix('categories')->group(function () {
    Route::get('search/{search}', [CategoryController::class, 'search']);
    Route::patch('{id}/status', [CategoryController::class, 'updateStatus']);
});

//Option routes
Route::apiResource('options', OptionController::class);


// User routes
Route::prefix('users')->group(function () {
    Route::get('search/{search}', [UserController::class, 'search']);
    Route::patch('{id}/status', [UserController::class, 'updateStatus']);
});
Route::apiResource('users', UserController::class);

