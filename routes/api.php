<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\BrandsController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;

Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'getUser']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('profile', [AuthController::class, 'userProfile']);
    });
});

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::apiResource('brands', BrandsController::class);
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);

    Route::get('get-order-items/{id}', [OrderController::class, 'GetOrderItems']);
    Route::get('get-order-items-by-user', [OrderController::class, 'GetOrderItemsByUser']);
    Route::post('change-status/{id}', [OrderController::class, 'changeStatus']);
});
