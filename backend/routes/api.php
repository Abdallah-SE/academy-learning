<?php

use Illuminate\Http\Request;
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

Route::group([], function () {
    // Health check route
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    });

    // API Version 1 Routes
    Route::prefix('v1')->group(function () {
        // Include module routes
        Route::prefix('auth')->group(function () {
            require __DIR__.'/../Modules/Auth/routes/api.php';
        });

        Route::prefix('user')->group(function () {
            require __DIR__.'/../Modules/User/routes/api.php';
        });

        Route::prefix('admin')->group(function () {
            require __DIR__.'/../Modules/Admin/routes/api.php';
        });
    });

    // API Version 2 Routes (for future use)
    Route::prefix('v2')->group(function () {
        // Future v2 routes will go here
        Route::get('/health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'version' => '2.0.0'
            ]);
        });
    });

    // Fallback route for undefined API endpoints
    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'API endpoint not found',
            'code' => 404
        ], 404);
    });
});
