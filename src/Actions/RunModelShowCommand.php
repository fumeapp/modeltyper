<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Commands\ShowModelCommand;
use FumeApp\ModelTyper\Exceptions\NestedCommandException;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;

class RunModelShowCommand
{
    use InteractsWithConsole;

    protected Application $app;

    public function __construct(Application $app = null)
    {
        $this->app = $app ?? app();
    }

    /**
     * Run internal Laravel model:show command.
     *
     * @see https://github.com/laravel/framework/blob/9.x/src/Illuminate/Foundation/Console/ShowModelCommand.php
     *
     * @param  string  $model
     * @return array
     *
     * @throws NestedCommandException
     *
     */
    public function __invoke(string $model): array
    {
        $relationships = implode(',', Arr::flatten(config('modeltyper.custom_relationships', [])));

        $commandArgs = [
            'model' => $model,
            '--json' => true,
            '--no-interaction' => true,
        ];

        if(! empty($relationships)) {
            $commandArgs['--custom-relationships'] = $relationships;
        }

        $command = ShowModelCommand::class;
        $exitCode = $this->runCommandWithoutMockOutput($command, $commandArgs);
        $output = Artisan::output();

        // NOTE this check should not fail under normal circumstances, but might be useful to catch
        // unexpected errors if running the command without mock fails for some reason.
        if (empty($output)) {
            $msg = "Could not resolve types for model '$model', Artisan::output() is empty.";
            $msg .= PHP_EOL . 'If you are running tests, make sure to set {public $mockConsoleOutput = false;}';
            throw new NestedCommandException($msg);
        }

        if ($exitCode !== Command::SUCCESS) {
            $msg = "Command '$command' failed with the following output: $output";
            throw new NestedCommandException($msg);
        }

        return json_decode($output, true);
    }

    /**
     * Run an Artisan command without testing mock output to prevent the command output
     * from being intercepted by mock output handler. Resets the mock output handler after
     * command execution so that mock testing is not disturbed.
     *
     * @param string $command Name of the command to run.
     * @param array<string, mixed> $args Arguments for the command.
     * @return int $exitCode Exit code returned by the command.
     *
     */
    private function runCommandWithoutMockOutput(string $command, array $args = []) : int
    {
        $originalOutput = $this->outputMocked() ? $this->app->get(OutputStyle::class) : false;
        $this->withoutMockingConsoleOutput();

        $exitCode = Artisan::call($command, $args);

        if($originalOutput) {
            $this->app->bind(OutputStyle::class, fn() => $originalOutput);
        }

        return $exitCode;
    }

    /**
     * Check if the console output is being mocked.
     *
     * @return bool $mocked
     *
     */
    private function outputMocked() : bool
    {
        if(! app()->runningInConsole()) {
            return false;
        }

        if(! config('app.env') === 'testing') {
            return false;
        }

        return $this->app->has(OutputStyle::class);
    }
}
