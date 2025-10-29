<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how cross-origin requests should be handled. These settings
    | enable the API to interact with a SPA client while still keeping the
    | allowed origins controlled through environment variables.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost,http://localhost:3000,http://localhost:5173,http://localhost:8000'
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
