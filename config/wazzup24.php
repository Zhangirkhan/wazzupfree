<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wazzup24 API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Wazzup24 WhatsApp API integration
    |
    */

    'api' => [
        'key' => env('WAZZUP24_API_KEY'),
        'url' => env('WAZZUP24_API_URL', 'https://api.wazzup24.com/v3'),
        'channel_id' => env('WAZZUP24_CHANNEL_ID'),
        'webhook_url' => env('WAZZUP24_WEBHOOK_URL'),
    ],

    'chat' => [
        'default_organization_id' => env('WAZZUP24_DEFAULT_ORG_ID', 1),
    ],

    'logging' => [
        'enabled' => env('WAZZUP24_LOG_ENABLED', true),
        'channel' => env('WAZZUP24_LOG_CHANNEL', 'daily'),
    ],

    'webhook' => [
        'verify_ssl' => env('WAZZUP24_VERIFY_SSL', true),
        'timeout' => env('WAZZUP24_TIMEOUT', 30),
    ],
];