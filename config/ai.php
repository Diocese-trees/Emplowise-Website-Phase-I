<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Alibaba Cloud Model Studio (OpenAI compatible)
    |--------------------------------------------------------------------------
    |
    | Every AI endpoint in this application talks to Model Studio through its
    | OpenAI compatible chat completions surface. The primary model runs first.
    | The fallback model runs only when the primary call fails.
    |
    */

    'alibaba' => [
        'api_key' => env('ALIBABA_CLOUD_API_KEY'),
        'base_url' => env('ALIBABA_CLOUD_BASE_URL', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'),
        'primary_model' => env('ALIBABA_CLOUD_PRIMARY_MODEL', 'qwen-max'),
        'fallback_model' => env('ALIBABA_CLOUD_FALLBACK_MODEL', 'kimi'),
        'timeout' => (int) env('ALIBABA_CLOUD_TIMEOUT', 45),
        'connect_timeout' => (int) env('ALIBABA_CLOUD_CONNECT_TIMEOUT', 10),
    ],

];
