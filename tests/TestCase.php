<?php

namespace Shovel\Tests;

use Illuminate\Support\Facades\Schema;
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
            \Shovel\ShovelServiceProvider::class,
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

        $app['config']->set('shovel.includePaginationLinks', true);
        $app['config']->set('shovel.omitEmptyObject', false);
        $app['config']->set('shovel.omitEmptyArray', false);

        Schema::dropIfExists('samples');
        Schema::create('samples', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }
}
