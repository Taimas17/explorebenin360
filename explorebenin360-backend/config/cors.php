<?php

return [
    'paths' => ['api/*'],
    'allowed_origins' => array_filter(
        explode(',', env('FRONTEND_ORIGIN', '')),
        fn($origin) => !empty($origin)
    ),
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'Origin'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
