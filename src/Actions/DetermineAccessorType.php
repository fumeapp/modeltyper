<?php

namespace FumeApp\ModelTyper\Actions;

use Exception;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class DetermineAccessorType
{
    /**
     * Determine the type of accessor.
     *
     * @see https://laravel.com/docs/9.x/eloquent-mutators#defining-an-accessor
     * @see https://laravel.com/docs/8.x/eloquent-mutators#defining-an-accessor
     *
     * @throws Exception
     */
    public function __invoke(ReflectionClass $reflectionModel, string $mutator): ReflectionMethod
    {
        // Try traditional
        try {
            $accessor = 'get' . Str::studly($mutator) . 'Attribute';

            return $reflectionModel->getMethod($accessor);
        } catch (Exception $e) {
        }

        // Try new
        try {
            $method = Str::studly($mutator);

            return $reflectionModel->getMethod($method);
        } catch (Exception $e) {
        }

        throw new Exception('Accessor method for ' . $mutator . ' on model ' . $reflectionModel->getName() . ' does not exist');
    }
}
