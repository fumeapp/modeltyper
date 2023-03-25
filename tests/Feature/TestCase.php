<?php

namespace Tests\Feature;

use FumeApp\ModelTyper\ModelTyperServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ModelTyperServiceProvider::class
        ];
    }

    public static function applicationBasePath()
{
    return ROOT_PATH . '/tests/skeleton';
}

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
