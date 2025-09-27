<?php

namespace FumeApp\ModelTyper\Traits;

use Illuminate\Support\Str;

trait ClassBaseName
{
    /**
     * Get the name of the class.
     */
    public function getClassName(string $className): string
    {
        return Str::afterLast($className, '\\');
    }
}
