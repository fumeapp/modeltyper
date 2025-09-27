<?php

namespace Tests\Traits;

use ReflectionClass;

trait ResolveClassAsReflection
{
    /**
     * Resolve a class as a ReflectionClass.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return \ReflectionClass<\Illuminate\Database\Eloquent\Model>
     */
    public function resolveClassAsReflection(string $model): ReflectionClass
    {
        return new ReflectionClass($model);
    }
}
