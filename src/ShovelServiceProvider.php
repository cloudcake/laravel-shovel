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
     * @var boolean
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

        Response::macro('withError', function ($message, $code = null) {
            if (is_numeric($message) && is_null($code)) {
                return app('shovel')->withError(null, $message);
            }

            return app('shovel')->withError($message, $code ?? 422);
        });

        Response::macro('withErrors', function ($message, $code = null) {
            if (is_numeric($message) && is_null($code)) {
                return app('shovel')->withError(null, $message);
            }

            return app('shovel')->withError($message, $code ?? 422);
        });

        ResponseFactory::macro('shovel', function ($data = null, $code = 200) {
            if (is_numeric($data) && is_null($code)) {
                $code = $data;
                $data = null;
            }

            $shovel = app('shovel');
            $shovel->provideData($data, $code);

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
