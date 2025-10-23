<?php

return [
    'provider' => env('MEDIA_PROVIDER', 'cloudinary'),
    'max_size_mb' => env('MEDIA_MAX_SIZE_MB', 15),

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'base_url' => env('CLOUDINARY_BASE_URL'),
    ],

    's3' => [
        'disk' => env('MEDIA_S3_DISK', 's3'),
        'cdn_base_url' => env('CDN_BASE_URL'),
    ],

    'allowed_mimes' => [
        'image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/avif',
        'video/mp4',
    ],
];
