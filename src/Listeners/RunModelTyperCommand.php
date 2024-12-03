<?php

namespace FumeApp\ModelTyper\Listeners;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Console\Kernel as Artisan;

class RunModelTyperCommand
{
    /**
     * Tracks whether we should run the models command on the CommandFinished event or not.
     * Set to true by the MigrationsEnded event, needs to be cleared before artisan call to prevent infinite loop.
     */
    public static bool $shouldRun = false;

    public function __construct(protected Artisan $artisan) {}

    /**
     * Handle the event.
     */
    public function handle(CommandFinished $event)
    {
        if (! self::$shouldRun) {
            return;
        }

        self::$shouldRun = false;

        $this->artisan->call(ModelTyperCommand::class, [], $event->output);
    }
}
