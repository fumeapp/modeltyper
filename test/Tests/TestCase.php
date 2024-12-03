<?php

namespace Tests;

use FumeApp\ModelTyper\ModelTyperServiceProvider;
use Illuminate\Config\Repository;

use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench_path;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ModelTyperServiceProvider::class,
        ];
    }

    public static function applicationBasePath(): string
    {
        return package_path('test/laravel-skeleton');
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config) {
            $config->set('database.default', 'testbench');
            $config->set('database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });
    }

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            workbench_path('database/migrations')
        );
    }
}
