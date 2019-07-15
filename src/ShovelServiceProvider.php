<?php

namespace Shovel;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
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
            __DIR__.'/Http/Middleware/StubMiddleware.php' => app_path('Http/Middleware/ApiResponse.php'),
        ], 'middleware');

        $this->registerMacros();
    }

    /**
     * Register Shovel macros.
     *
     * @return void
     */
    private function registerMacros()
    {
        Response::macro('withMeta', function ($key, $value) {
            Arr::set($this->additionalMeta, $key, $value);
            return $this;
        });
    }
}
