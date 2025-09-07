<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\V1\AdminAuthController;
use Modules\Admin\Http\Controllers\V1\AdminAvatarController;
use Modules\Admin\Http\Controllers\V1\AdminController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Admin module.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1/admin')->group(function () {
    Route::get('/test', function () {
        return response()->json(['ok' => true]);
    });

    // Admin Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AdminAuthController::class, 'login']);
        // ->middleware('throttle:5,15'); // 5 attempts per 15 minutes
        Route::post('/refresh', [AdminAuthController::class, 'refresh']);
    });

    // Admin Protected Routes
    Route::middleware(['auth:admin'])->group(function () {

        // Authentication
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/profile', [AdminAuthController::class, 'profile']);
            Route::put('/profile', [AdminAuthController::class, 'updateProfile']);
        });

        Route::apiResource('admins', AdminController::class);
        Route::get('admins/trashed', [AdminController::class, 'trashed']); // Get soft deleted admins
        Route::delete('admins/{id}/force', [AdminController::class, 'forceDelete']); // Permanent delete
        Route::patch('admins/{id}/restore', [AdminController::class, 'restore']); // Restore soft deleted
        
        // Roles endpoint
        Route::get('roles', [AdminController::class, 'getAvailableRoles']);

        Route::put('/admins/{adminId}/avatar', [AdminController::class, 'uploadAvatar']);
        // Avatar Management (separate controller)
         Route::post('/admins/{adminId}/avatar', [AdminAvatarController::class, 'store']);
        Route::delete('/admins/{adminId}/avatar', [AdminAvatarController::class, 'destroy']);
    });
});
