<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',  // React frontend (dev)
        'http://localhost:5174',  // React frontend (fallback)
        'http://localhost:3001',  // React frontend (alt)
        'https://app.grserviciotecnico.com', // Produccion
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
