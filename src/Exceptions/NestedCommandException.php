<?php

namespace FumeApp\ModelTyper\Exceptions;

/**
 * Exception that should be thrown when an underlying command fails to execute.
 */
class NestedCommandException extends CommandException
{
    public function wasCausedBy(string $exceptionClass) : bool
    {
        return $this->getPrevious() instanceof $exceptionClass;
    }
}
