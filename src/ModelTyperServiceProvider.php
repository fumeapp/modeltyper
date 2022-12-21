<?php

namespace FumeApp\ModelTyper;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Illuminate\Support\ServiceProvider;

class ModelTyperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModelTyperCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
