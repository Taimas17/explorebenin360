<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET', 'OPTIONS'],
    'allowed_origins' => [env('FRONTEND_ORIGIN', '*')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
