<?php

namespace Shovel\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Shovel\Tests\Models\Sample;

abstract class TestCase extends BaseTestCase
{
    public function setup()
    {
        parent::setup();

        $this->app->setBasePath(__DIR__.'/../');
        $this->createModels(150);
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

        Schema::dropIfExists('samples');
        Schema::create('samples', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });
    }

    public function createModels($count)
    {
        for ($i = 0; $i < $count; $i++) {
            Sample::create([
            'name'        => "Shovel Model #{$i}",
            'description' => 'Shovel Test Model',
          ]);
        }
    }
}
