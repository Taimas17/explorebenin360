<?php

return [
    'commission_percent' => env('COMMISSION_PERCENT', 12),
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),
        'secret_key' => env('PAYSTACK_SECRET_KEY', ''),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'callback_url' => env('FRONTEND_URL', 'http://localhost:5173') . '/checkout/callback',
    ],
];
