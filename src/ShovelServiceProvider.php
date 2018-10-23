<?php

namespace Shovel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class ShovelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot up Macros.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerShovelSingleton();
        $this->registerResponseMacros();
    }

    private function registerShovelSingleton()
    {
        $this->app->singleton('shovel', function ($app) {
            return new \Shovel\Shovel();
        });
    }

    private function registerResponseMacros()
    {
        Response::macro('withMeta', function ($keys, $data) {
            return app('shovel')->withMeta($keys, $data);
        });

        Response::macro('withMessage', function ($message = '') {
            return app('shovel')->withMessage($message);
        });

        Response::macro('withError', function ($message = '', $code = 422) {
            return app('shovel')->withError($message, $code);
        });

        Response::macro('withErrors', function ($message = '', $code = 422) {
            return app('shovel')->withError($message, $code);
        });

        ResponseFactory::macro('shovel', function ($data = null, $status_code = 200) {
            $shovel = app('shovel');
            $shovel->provideData($data, $status_code);

            return $shovel->responseInstance();
        });
    }
}
