<?php

namespace FumeApp\ModelTyper;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use FumeApp\ModelTyper\Commands\ShowModelTyperMappingsCommand;
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
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
