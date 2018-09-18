<?php

namespace Shovel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ShovelServiceProvider extends LaravelServiceProvider
{
    /**
     * Boot up Macros.
     *
     * @return void
     */
    public function boot()
    {
        new ShovelMacroRegister();
    }
}
