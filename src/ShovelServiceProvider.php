<?php

namespace Shovel;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ShovelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot up Shovel.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerShovelSingleton();
        $this->registerResponseMacros();
        $this->registerConfiguration();
    }

    /**
     * Register payload singleton.
     *
     * @return void
     */
    private function registerShovelSingleton()
    {
        $this->app->singleton('shovel', function ($app) {
            return new \Shovel\Shovel();
        });
    }

    /**
     * Register the macros.
     *
     * @return void
     */
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

            return $shovel->getResponse();
        });
    }

    /**
     * Register shovel configuration.
     *
     * @return void
     */
    private function registerConfiguration()
    {
        $this->publishes([
            __DIR__.'/Config/shovel.php' => config_path('shovel.php'),
        ], 'config');
    }
}
