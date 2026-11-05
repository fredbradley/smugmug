<?php

return [
    // Your application's API key and secret, from https://www.smugmug.com/app/developer (Apply for an API Key).
    // The key alone reads public data; the secret signs OAuth 1.0a requests.
    'api_key' => env('SMUGMUG_API_KEY'),

    'api_secret' => env('SMUGMUG_API_SECRET'),

    // The access token and secret of the account the application acts as by default: yours, for a site that shows
    // or manages one SmugMug account. Leave them empty to read public data with the API key alone, or to act for
    // each of your users with SmugMug::withToken($token, $secret) after they have signed in with OAuth.
    'access_token' => env('SMUGMUG_ACCESS_TOKEN'),

    'access_token_secret' => env('SMUGMUG_ACCESS_TOKEN_SECRET'),

    // Where SmugMug sends the user back after they approve your application (a route of yours), or "oob" to have
    // SmugMug show them a six-digit code to type in instead.
    'callback_url' => env('SMUGMUG_CALLBACK_URL', 'oob'),

    // Other applications or accounts, used with SmugMug::connection('name'):
    // 'connections' => [
    //     'studio' => [
    //         'api_key' => env('SMUGMUG_STUDIO_API_KEY'),
    //         'api_secret' => env('SMUGMUG_STUDIO_API_SECRET'),
    //         'access_token' => env('SMUGMUG_STUDIO_ACCESS_TOKEN'),
    //         'access_token_secret' => env('SMUGMUG_STUDIO_ACCESS_TOKEN_SECRET'),
    //     ],
    // ],
    'connections' => [],

    'timeouts' => [
        'connect' => env('SMUGMUG_CONNECT_TIMEOUT', 10),
        'request' => env('SMUGMUG_REQUEST_TIMEOUT', 30),
        // Uploads send the whole file in one request, so they get longer.
        'upload' => env('SMUGMUG_UPLOAD_TIMEOUT', 300),
    ],

    'retries' => [
        'times' => env('SMUGMUG_RETRIES'),
        'interval_ms' => 500,
    ],

    'cache' => [
        'enabled' => env('SMUGMUG_CACHE', false),
        'store' => env('SMUGMUG_CACHE_STORE'),
        'seconds' => 3600,
    ],

    'pagination' => [
        // SmugMug clamps a page to each endpoint's own maximum; 100 is within every one.
        'per_page' => 100,
    ],
];
