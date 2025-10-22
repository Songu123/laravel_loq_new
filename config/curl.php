<?php

return [
    /*
    |--------------------------------------------------------------------------
    | cURL SSL Verification
    |--------------------------------------------------------------------------
    |
    | This option controls whether cURL should verify SSL certificates.
    | Set to false ONLY in development if you encounter SSL certificate errors.
    | NEVER set to false in production!
    |
    */

    'verify' => env('CURL_SSL_VERIFYPEER', true),

    /*
    |--------------------------------------------------------------------------
    | CA Certificate Bundle Path
    |--------------------------------------------------------------------------
    |
    | Path to the CA certificate bundle file.
    | Download from: https://curl.se/ca/cacert.pem
    | Recommended: C:\cacert\cacert.pem
    |
    */

    'cainfo' => env('CURL_CAINFO', null),
];
