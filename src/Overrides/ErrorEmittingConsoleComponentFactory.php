<?php

namespace FumeApp\ModelTyper\Overrides;

use FumeApp\ModelTyper\Exceptions\CommandException;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A class ment to warp and override the default console component factory
 */
class ErrorEmittingConsoleComponentFactory
{
    private Factory $factory;

    private bool $throwExceptions;

    public function __construct(Factory $factory, bool $throwExceptions = false)
    {
        $this->factory = $factory;
        $this->throwExceptions = $throwExceptions;
    }

    /**
     * Reroute calls to underlying instance unless intercepted by override.
     */
    public function __call($name, $arguments)
    {
        if ($name === 'error') {
            return $this->emitError(...$arguments);
        }

        return $this->factory->$name(...$arguments);
    }

    private function emitError(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL, ?string $exceptionClass = CommandException::class): int
    {
        if ($this->throwExceptions) {
            throw new $exceptionClass(message: $message);
        }

        return Command::FAILURE;
    }
}
