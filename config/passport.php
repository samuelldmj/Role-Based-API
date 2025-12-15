<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Passport will use when
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'api',

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Passport Database Connection
    |--------------------------------------------------------------------------
    |
    | By default, Passport's models will utilize your application's default
    | database connection. If you wish to use a different connection you
    | may specify the configured name of the database connection here.
    |
    */

    'connection' => env('PASSPORT_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Client UUIDs
    |--------------------------------------------------------------------------
    |
    | By default, Passport uses auto-incrementing primary keys when assigning
    | IDs to clients. However, if Passport is installed using the provided
    | --uuids switch, this will be set to "true" and UUIDs will be used.
    |
    */

    'client_uuids' => true,

    /*
    |--------------------------------------------------------------------------
    | Personal Access Tokens Expire
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of minutes that personal access tokens
    | should remain valid. By default, they remain valid for one year.
    |
    */

    'personal_access_tokens_expire_in' => null,

    /*
    |--------------------------------------------------------------------------
    | Access Token Expire
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of minutes that access tokens should
    | remain valid. By default, they remain valid for 15 minutes.
    |
    */

    'tokens_expire_in' => null,

    /*
    |--------------------------------------------------------------------------
    | Refresh Token Expire
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of minutes that refresh tokens should
    | remain valid. By default, they remain valid for 30 days.
    |
    */

    'refresh_tokens_expire_in' => null,

];
