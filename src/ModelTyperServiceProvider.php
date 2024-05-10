<?php

namespace FumeApp\ModelTyper;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use FumeApp\ModelTyper\Commands\ShowModelCommand;
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
                ShowModelCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
