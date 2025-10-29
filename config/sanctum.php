<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production SPA domain names where the frontend is hosted.
    |
    */

    'stateful' => array_filter(array_map('trim', explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:5173,localhost:8000,',
        parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
    ))))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that should be checked
    | when Sanctum attempts to authenticate a request. If none of these
    | guards are able to authenticate the request, Sanctum will check
    | the bearer token on the incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will
    | be considered expired. This will not affect personal access tokens.
    | If this value is null, Sanctum tokens do not expire automatically.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Here you may specify the prefix that should be applied to token values
    | to avoid conflicts with other password managers, etc.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the pipeline middleware. You may change the listed
    | middleware but ensure `EnsureFrontendRequestsAreStateful` remains.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
        'add_cookies_to_response' => Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        'start_session' => Illuminate\Session\Middleware\StartSession::class,
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'ensure_csrf_cookie' => Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanctum Storage Strategy
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage driver that will be used when
    | storing API tokens issued by Sanctum.
    |
    */

    'store' => [
        'driver' => env('SANCTUM_STORE_DRIVER', 'database'),
        'store' => env('SANCTUM_STORE', null),
    ],

    'prefix' => env('SANCTUM_TABLE_PREFIX', ''),

    'personal_access_token_model' => Sanctum::personalAccessTokenModel(),
];
