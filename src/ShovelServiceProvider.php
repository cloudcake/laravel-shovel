<?php

namespace Shovel;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ShovelServiceProvider extends ServiceProvider
{
    /**
     * Boot up Shovel.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/shovel.php' => config_path('shovel.php'),
        ], 'config');

        $middleware = config('shovel.middleware', [
            'request' => \Shovel\Http\Middleware\ApiRequest::class,
            'response' => \Shovel\Http\Middleware\ApiResponse::class,
        ]);

        $this->app['router']->aliasMiddleware('ApiRequest', $middleware['request']);
        $this->app['router']->aliasMiddleware('ApiResponse', $middleware['response']);

        $withMeta = function ($key, $value) {
            Arr::set($this->additionalMeta, $key, $value);
            return $this;
        };

        Response::macro('withMeta', $withMeta);
        JsonResponse::macro('withMeta', $withMeta);
        ResponseFactory::macro('withMeta', $withMeta);
    }
}
