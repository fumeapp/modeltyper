<?php

namespace FumeApp\ModelTyper\Traits;

use ReflectionClass;

trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  array<string, mixed>  $info
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        $class = $info['class'];

        return new ReflectionClass($class);
    }
}
