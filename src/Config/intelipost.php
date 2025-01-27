<?php

return [
    'api_key' => env('INTELIPOST_API_KEY'),
    'api_url' => env('INTELIPOST_API_URL', 'https://api.intelipost.com.br/api/v1'),
    'default_origin_zip_code' => env('INTELIPOST_DEFAULT_ORIGIN_ZIP_CODE'),
];
