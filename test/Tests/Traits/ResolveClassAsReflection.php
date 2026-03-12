<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait ResolveClassAsReflection
{
    /**
     * Resolve a class as a ReflectionClass.
     *
     * @param  class-string<Model>  $model
     * @return ReflectionClass<Model>
     */
    public function resolveClassAsReflection(string $model): ReflectionClass
    {
        return new ReflectionClass($model);
    }
}
