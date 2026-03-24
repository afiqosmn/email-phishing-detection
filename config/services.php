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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'scopes' => env('GOOGLE_SCOPES'),
    ],
    
    'google_safe_browsing' => [
        'key' => env('GOOGLE_SAFE_BROWSING_KEY'),
        'endpoint' => 'https://safebrowsing.googleapis.com/v4/threatMatches:find',
    ],

    'ml' => [
        'endpoint' => env('ML_API_ENDPOINT', 'http://127.0.0.1:5001/detect'),
        'url' => env('ML_API_URL', 'http://127.0.0.1:5001'),
        'timeout' => env('ML_API_TIMEOUT', 30),
    ],

];
