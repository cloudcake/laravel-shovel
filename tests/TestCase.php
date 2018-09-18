<?php

namespace Moonlight\Tests;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setup()
    {
        parent::setup();

        $this->app->setBasePath(__DIR__.'/../');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Moonlight\MoonlightServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
