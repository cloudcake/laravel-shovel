<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may define your own custom middleware if needed - when doing so
    | the classes must extend the respective shovel middleware class.
    |
    */
    'middleware' => [
        'request' => \Shovel\Http\Middleware\ApiRequest::class,
        'response' => \Shovel\Http\Middleware\ApiResponse::class,
    ],
];
