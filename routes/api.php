<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\Auth\AuthController;


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
