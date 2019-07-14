<?php

namespace Shovel;

use Shovel\Shovel;
use Illuminate\Http\Response;
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
            __DIR__.'/../Config/shovel.php' => config_path('shovel.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Http/Middleware/StubMiddleware.php' => app_path('Http/Middleware/ApiResponse.php'),
        ], 'config');
    }
}
