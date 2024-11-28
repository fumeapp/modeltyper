<?php

namespace FumeApp\ModelTyper\Actions;

use Exception;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class DetermineAccessorType
{
    /**
     * Determine the type of accessor.
     *
     * @see https://laravel.com/docs/9.x/eloquent-mutators#defining-an-accessor
     *
     * @param  \ReflectionClass<\Illuminate\Database\Eloquent\Model>  $reflectionModel
     *
     * @throws Exception
     */
    public function __invoke(ReflectionClass $reflectionModel, string $mutator): ReflectionMethod
    {
        $mutator = Str::studly($mutator);

        // Try traditional
        try {
            return $reflectionModel->getMethod('get' . $mutator . 'Attribute');
        } catch (ReflectionException $e) {
        }

        // Try new
        try {
            return $reflectionModel->getMethod($mutator);
        } catch (ReflectionException $e) {
        }

        throw new Exception('Accessor method for ' . $mutator . ' on model ' . $reflectionModel->getName() . ' does not exist');
    }
}
