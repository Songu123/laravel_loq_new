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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],

    'python_api' => [
        'url' => env('PYTHON_API_URL', 'http://127.0.0.1:5001'),
    ],

    'face_recognition' => [
        'url' => env('FACE_RECOGNITION_API_URL', 'http://127.0.0.1:8001'),
        'enabled' => env('FACE_RECOGNITION_ENABLED', true),
        'threshold' => env('FACE_RECOGNITION_THRESHOLD', 0.6),
        'cache_ttl' => env('FACE_RECOGNITION_CACHE_TTL', 3600),
    ],

];
