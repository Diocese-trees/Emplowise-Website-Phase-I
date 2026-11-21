<?php

declare(strict_types=1);

return [
    /*
     * Public URL of the static build. For a GitHub Pages project site this is
     * https://<user>.github.io/<repo>. Override with STATIC_BASE_URL.
     */
    'base_url' => env('STATIC_BASE_URL', 'https://diocese-trees.github.io/Emplowise-Website-Phase-I'),

    /*
     * Optional Web3Forms access key. With a key, the Free HR Audit form on the
     * static build posts to Web3Forms and emails the submission. Without one,
     * the form is still rebuilt but marked inert so it never posts nowhere.
     */
    'web3forms_key' => env('WEB3FORMS_ACCESS_KEY', ''),
];
