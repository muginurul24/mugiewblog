<?php

return [

    'paths' => ['api/*', 'livewire-*/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', env('APP_URL', ''))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-CSRF-TOKEN', 'X-Requested-With', 'Accept', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
