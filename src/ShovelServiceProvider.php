<?php

namespace Shovel;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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

        Response::macro('withMeta', function ($key, $value) {
            Arr::set($this->additionalMeta, $key, $value);
            return $this;
        });
    }
}
