<?php

namespace FumeApp\ModelTyper\Traits;

use FumeApp\ModelTyper\Actions\RunModelInspector;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

/**
 * @phpstan-import-type ModelInspectorResult from RunModelInspector
 */
trait ModelRefClass
{
    /**
     * Get the reflection interface.
     *
     * @param  ModelInspectorResult  $info
     * @return ReflectionClass<Model>
     */
    public function getRefInterface(array $info): ReflectionClass
    {
        return new ReflectionClass($info['class']);
    }
}
