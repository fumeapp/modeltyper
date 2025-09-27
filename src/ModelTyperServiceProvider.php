<?php

namespace FumeApp\ModelTyper;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use FumeApp\ModelTyper\Commands\ShowModelTyperMappingsCommand;
use FumeApp\ModelTyper\Listeners\RunModelTyperCommand;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\ServiceProvider;

class ModelTyperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/modeltyper.php' => config_path('modeltyper.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelTyperCommand::class,
                ShowModelTyperMappingsCommand::class,
            ]);
        }

        $this->app->singleton(ModelTyperCommand::class, function ($app) {
            return new ModelTyperCommand($app['files']);
        });

        if (! $this->app->runningUnitTests() && $this->app['config']->get('modeltyper.run-after-migrate', false) && $this->app['config']->get('modeltyper.output-file', false)) {
            $this->app['events']->listen(CommandFinished::class, RunModelTyperCommand::class);
            $this->app['events']->listen(MigrationsEnded::class, function () {
                RunModelTyperCommand::$shouldRun = true;
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/modeltyper.php',
            'modeltyper'
        );
    }
}
