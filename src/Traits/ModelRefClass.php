<?php

namespace FumeApp\ModelTyper\Traits;

use ReflectionClass;

trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  array  $info - The model details from the model:show command.
     * @return ReflectionClass
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        $class = $info['class'];

        return new ReflectionClass($class);
    }
}
