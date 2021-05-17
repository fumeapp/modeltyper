<?php

namespace ModelTyper;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ModelTyperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                \ModelTyper\Commands\ModelTyper::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
