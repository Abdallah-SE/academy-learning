<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Pagination Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the default pagination settings for the entire
    | application. All modules should use these settings for consistency.
    |
    */

    'default_per_page' => 10,
    'max_per_page' => 100,
    'min_per_page' => 1,
    
    /*
    |--------------------------------------------------------------------------
    | Module Specific Pagination Settings
    |--------------------------------------------------------------------------
    |
    | You can override the default per_page for specific modules if needed.
    | This allows for flexibility while maintaining consistency.
    |
    */
    
    'modules' => [
        'admin' => [
            'default_per_page' => 10,
        ],
        'user' => [
            'default_per_page' => 10,
        ],
        'membership' => [
            'default_per_page' => 10,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Pagination Options
    |--------------------------------------------------------------------------
    |
    | Available pagination options that can be used in dropdowns
    | or selection interfaces.
    |
    */
    
    'options' => [5, 10, 25, 50, 100],
];