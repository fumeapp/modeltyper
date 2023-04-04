<?php

namespace Tests\Traits;

use ReflectionClass;

trait ResolveClassAsReflection
{
    /**
     * Resolve a class as a ReflectionClass.
     *
     * @param  class-string  $model
     */
    public function resolveClassAsReflection(string $model): ReflectionClass
    {
        return new ReflectionClass($model);
    }
}
