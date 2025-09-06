<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the API endpoints and Next.js integration
    |
    */

    'version' => env('API_VERSION', 'v1'),
    
    'prefix' => env('API_PREFIX', 'api'),
    
    'middleware' => [
        'cors',
        'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration
    |--------------------------------------------------------------------------
    */
    'cors' => [
        'allowed_origins' => env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000'),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-XSRF-TOKEN'],
        'exposed_headers' => [],
        'max_age' => 86400,
        'supports_credentials' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('API_RATE_LIMITING', true),
        'max_attempts' => env('API_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('API_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'guard' => env('API_AUTH_GUARD', 'api'),
        'token_expiry' => env('JWT_TTL', 60), // minutes
        'refresh_token_expiry' => env('JWT_REFRESH_TTL', 20160), // minutes (14 days)
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Format
    |--------------------------------------------------------------------------
    */
    'response' => [
        'include_timestamp' => true,
        'include_status_code' => true,
        'default_success_message' => 'Success',
        'default_error_message' => 'Error',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 10,
        'max_per_page' => 100,
        'default_sort' => 'created_at',
        'default_direction' => 'desc',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload
    |--------------------------------------------------------------------------
    */
    'file_upload' => [
        'max_size' => env('FILE_UPLOAD_MAX_SIZE', 5120), // KB
        'allowed_types' => ['jpeg', 'png', 'jpg', 'gif', 'webp'],
        'storage_disk' => env('FILE_STORAGE_DISK', 'public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('API_LOGGING', true),
        'log_requests' => env('API_LOG_REQUESTS', true),
        'log_responses' => env('API_LOG_RESPONSES', false),
        'log_errors' => env('API_LOG_ERRORS', true),
    ],
];
