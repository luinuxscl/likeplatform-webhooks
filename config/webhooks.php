<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for outgoing and incoming webhooks.
    |
    */

    // Secret key for validating incoming webhooks
    'incoming_secret' => env('WEBHOOK_INCOMING_SECRET'),

    // Maximum number of retry attempts for failed deliveries
    'max_retries' => 3,

    // Timeout in seconds for webhook HTTP requests
    'timeout' => 10,

    // Events that are available for subscription
    'events' => [
        'user.created',
        'user.updated',
        'api_key.created',
        'api_key.revoked',
    ],
];
