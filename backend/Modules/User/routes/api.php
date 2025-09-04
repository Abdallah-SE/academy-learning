<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::prefix('users')->group(function () {
    // Public routes (if any)
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/find/name', [UserController::class, 'findByName']);
    
    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
    });
});
