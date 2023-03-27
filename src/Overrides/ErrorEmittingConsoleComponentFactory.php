<?php

namespace FumeApp\ModelTyper\Overrides;

use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Factory;

/**
 * A class ment to warp and override the default console component factory
 *
 */
class ErrorEmittingConsoleComponentFactory
{
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Reroute calls to underlying instance unless intercepted by override.
     *
     */
    public function __call($name, $arguments)
    {
        $result = $this->factory->$name(...$arguments);

        if($name === 'error') {
            return Command::FAILURE;
        }

        return $result;
    }
}
