<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Overrides\ErrorEmittingConsoleComponentFactory;
use Illuminate\Database\Console\ShowModelCommand as BaseCommand;

/**
 * A wrapper command for Laravel default model:show to add customizaton for model generation.
 *
 */
class ShowModelCommand extends BaseCommand
{
    protected $name = 'model:typer-show {model}';

    protected $signature = 'model:typer-show {model : The model to show}
                {--custom-relationships= : Custom relationships that should be included, separated by commas}
                {--database= : The database connection to use}
                {--json : Output the model as JSON}';

    public function handle()
    {
        // Override default console component factory to force parent command to return failed exit code on error.
        $this->components = new ErrorEmittingConsoleComponentFactory($this->components);

        if($this->option('custom-relationships')) {
            $customRelationships = collect(explode(',', $this->option('custom-relationships')))->map(fn($method) => trim($method));
            $this->relationMethods = array_merge($this->relationMethods, $customRelationships->toArray());
        }

        return parent::handle();
    }
}
