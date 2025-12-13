<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ID Hashing Salt
    |--------------------------------------------------------------------------
    |
    | Salt used when generating obfuscated IDs. Defaults to APP_KEY.
    |
    */
    'salt' => env('ID_HASH_SALT', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Minimum Length
    |--------------------------------------------------------------------------
    |
    | Minimum length of generated hashes. Keep short but non-trivial.
    |
    */
    'min_length' => (int) env('ID_HASH_MIN_LENGTH', 8),

    /*
    |--------------------------------------------------------------------------
    | Alphabet
    |--------------------------------------------------------------------------
    |
    | Allowed characters for generated hashes. Base62 keeps them compact.
    |
    */
    'alphabet' => env('ID_HASH_ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'),
];
