<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Exceptions\AbstractModelException;
use FumeApp\ModelTyper\Overrides\ErrorEmittingConsoleComponentFactory;
use Illuminate\Database\Console\ShowModelCommand as BaseCommand;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * A wrapper command for Laravel default model:show to add customization for model generation.
 */
#[AsCommand(name: 'model:typer-show')]
class ShowModelCommand extends BaseCommand
{
    protected $name = 'model:typer-show {model}';

    protected $signature = 'model:typer-show {model : The model to show}
                {--custom-relationships= : Custom relationships that should be included, separated by commas}
                {--resolve-abstract : Attempt to resolve the model even if it is abstract}
                {--throw-exceptions : Throw exceptions instead of caching them and outputting error format}
                {--database= : The database connection to use}
                {--json : Output the model as JSON}';

    /**
     * @override
     */
    public function handle()
    {
        // Override default console component factory to force parent command to return failed exit code on error.
        $this->components = new ErrorEmittingConsoleComponentFactory($this->components, $this->option('throw-exceptions')); // @phpstan-ignore-line

        if ($this->option('custom-relationships')) {
            $customRelationships = collect(explode(',', $this->option('custom-relationships')))->map(fn ($method) => trim($method));
            $this->relationMethods = array_merge($this->relationMethods, $customRelationships->toArray());
        }

        return parent::handle();
    }

    /**
     * @override
     */
    protected function qualifyModel(string $model)
    {
        $class = parent::qualifyModel($model);
        $reflection = new ReflectionClass($class);

        if ($reflection->isInterface() || $reflection->isTrait() || $reflection->isEnum()) {
            $msg = "Skipping '$model' as it is an interface/trait/enum.";
            $this->components->error($msg, OutputStyle::OUTPUT_NORMAL, AbstractModelException::class); // @phpstan-ignore-line
        }

        if ($reflection->isAbstract() && ! $this->option('resolve-abstract')) {
            $msg = "Trying to resolve an abstract model '$model' when 'resolve-abstract' option is not enabled.";
            $this->components->error($msg, OutputStyle::OUTPUT_NORMAL, AbstractModelException::class); // @phpstan-ignore-line
        }

        return $class;
    }
}
