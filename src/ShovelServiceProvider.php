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
     * The middleware that should handle HTTP requests.
     *
     * @var string
     */
    protected $requestMiddleware = \Shovel\Http\Middleware\ApiRequest::class;

    /**
     * The middleware that should handle HTTP responses.
     *
     * @var string
     */
    protected $responseMiddleware = \Shovel\Http\Middleware\ApiResponse::class;

    /**
     * Boot up Shovel.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->aliasMiddleware('ApiRequest', $this->requestMiddleware);
        $this->app['router']->aliasMiddleware('ApiResponse', $this->responseMiddleware);

        $withMeta = function ($key, $value) {
            Arr::set($this->additionalMeta, $key, $value);
            return $this;
        };

        Response::macro('withMeta', $withMeta);
        JsonResponse::macro('withMeta', $withMeta);
        ResponseFactory::macro('withMeta', $withMeta);
    }
}
