<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'einvoice' => [
        'base_url' => env('EINVOICE_BASE_URL', null),
        'qrcode_base_url' => env('EINVOICE_QRCODE_BASE_URL', null),
        'client_id' => env('EINVOICE_CLIENT_ID', null),
        'client_secret1' => env('EINVOICE_CLIENT_SECRET_1', null),
        'client_secret2' => env('EINVOICE_CLIENT_SECRET_2', null),
        'private_key_secret' => env('EINVOICE_PRIVATE_KEY_SECRET', null),
        'scope' => env('EINVOICE_SCOPE', 'InvoicingAPI'),
        'token_cache_key' => env('EINVOICE_TOKEN_CACHE_KEY', null),
        'token_expiry_buffer_seconds' => env('EINVOICE_TOKEN_EXPIRY_BUFFER_SECONDS', 300),
        'timezone' => env('EINVOICE_TIMEZONE', null),
        'private_key_passphrase' => env('EINVOICE_PRIVATE_KEY_PASSPHRASE', null),
    ],
    'api' => [
        'token' => env('API_TOKEN', null)
    ],
];
