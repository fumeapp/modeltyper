<?php

namespace FumeApp\ModelTyper\Commands;

use Illuminate\Foundation\Console\ShowModelCommand as BaseCommand;
use Illuminate\Support\Composer;

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

    // /**
    //  * Create a new command instance.
    //  *
    //  * @return void
    //  */
    // public function __construct(Composer $composer = null)
    // {
    //     parent::__construct($composer);

    //     $customMethods = collect(explode(',', $this->option('custom-relationships')))->map(fn($method) => trim($method));
    //     $this->relationMethods = array_merge($this->relationMethods, $customMethods);
    // }

    public function handle()
    {
        $customMethods = collect(explode(',', $this->option('custom-relationships')))->map(fn($method) => trim($method));
        $this->relationMethods = array_merge($this->relationMethods, $customMethods);

        parent::handle();
    }
}
