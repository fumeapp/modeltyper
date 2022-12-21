<?php

namespace FumeApp\ModelTyper\Traits;

use Illuminate\Support\Str;

trait ModelBaseName
{
    /**
     * Get the name of the model.
     *
     * @param  string  $modelName
     * @return string
     */
    public function getModelName(string $modelName): string
    {
        return Str::afterLast($modelName, '\\');
    }
}
