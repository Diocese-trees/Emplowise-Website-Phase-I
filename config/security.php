<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | mode: off | report_only | enforce
    |
    | Roll out in this order: report_only, watch /storage/logs/csp.log and the
    | browser console until it is silent, then flip to enforce.
    |
    */

    'csp' => [
        'mode' => env('SECURITY_CSP_MODE', 'report_only'),
        'report_uri' => env('SECURITY_CSP_REPORT_URI', '/api/csp-report'),
        'directives' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'"],
            'style-src' => ["'self'"],
            'img-src' => ["'self'", 'data:'],
            'font-src' => ["'self'"],
            'connect-src' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
            'base-uri' => ["'self'"],
            'object-src' => ["'none'"],
            'manifest-src' => ["'self'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Strict Transport Security
    |--------------------------------------------------------------------------
    |
    | Only ever emitted on requests already served over HTTPS. Preview tunnels
    | and plain HTTP hosts never receive it, so a local demo cannot poison the
    | browser with a permanent HTTPS requirement.
    |
    | preload is on because the site is HTTPS only: every page, asset, form
    | post, and redirect is served over TLS, so the preload token is safe to
    | send. Submitting the domain to hstspreload.org is a separate, long lived
    | decision. Do that only after every subdomain answers over HTTPS.
    |
    */

    'hsts' => [
        'enabled' => (bool) env('SECURITY_HSTS', true),
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 63072000),
        'include_subdomains' => (bool) env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('SECURITY_HSTS_PRELOAD', true),
    ],

];
