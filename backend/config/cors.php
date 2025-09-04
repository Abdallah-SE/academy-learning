<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'allowed_origins' => [
        // Local development
        'http://localhost:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
        
        // Production domains (add your actual domains here)
        'https://yourdomain.com',
        'https://www.yourdomain.com',
        'https://app.yourdomain.com',
        
        // Staging/Testing (if you have them)
        'https://staging.yourdomain.com',
        'https://dev.yourdomain.com',
    ],

    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-XSRF-TOKEN',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,
];
