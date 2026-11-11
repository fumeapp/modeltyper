<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use ReflectionClass;

class WriteFillable
{
    /**
     * Get model columns and attributes to the output.
     *
     * @param  ReflectionClass<Model>  $reflectionModel
     */
    public function __invoke(ReflectionClass $reflectionModel, string $name, string $fillableSuffix, string $indent = '', bool $noHidden = false): string
    {
        $case = Config::get('modeltyper.case.columns', 'snake');
        $match_case = app(MatchCase::class);

        $instance = $reflectionModel->newInstanceWithoutConstructor();

        $fillableAttributes = $noHidden ? Arr::exceptValues($instance->getFillable(), $instance->getHidden()) : $instance->getFillable();
        $fillablesUnion = empty($fillableAttributes)
            ? 'never'
            : implode(' | ', array_map(fn ($fillableAttribute) => "'{$match_case($case, $fillableAttribute)}'", $fillableAttributes));

        return "{$indent}export type {$name}{$fillableSuffix} = Pick<$name, $fillablesUnion>" . PHP_EOL;
    }
}
