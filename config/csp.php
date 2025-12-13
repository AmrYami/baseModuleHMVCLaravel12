<?php

return [

    /*
     * Presets determine which CSP headers will be set. A valid CSP preset is
     * any class that implements `Spatie\Csp\Preset`.
     */
    'presets' => [
        App\Policies\Csp\AppCspPolicy::class,
    ],

    /**
     * Register additional global CSP directives here.
     */
    'directives' => [
        //
    ],

    /*
     * These presets will be put in a report-only policy. This is great for testing out
     * a new policy or changes to existing CSP policy without breaking anything.
     */
    'report_only_presets' => env('CSP_REPORT_ONLY', true)
        ? [App\Policies\Csp\AppCspPolicy::class]
        : [],

    /**
     * Register additional global report-only CSP directives here.
     */
    'report_only_directives' => [
        //
    ],

    /*
     * All violations against the policy will be reported to this url.
     * A great service you could use for this is https://report-uri.com/
     *
     * You can override this setting by calling `reportTo` on your policy.
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /**
     * Headers will be added when Vite is hot reloading.
     */
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => Spatie\Csp\Nonce\RandomString::class,

    /*
     * Set to false to disable automatic nonce generation and handling.
     * This is useful when you want to use 'unsafe-inline' for scripts/styles
     * and cannot add inline nonces. 
     * Note that this will make your CSP policy less secure.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];
