<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class WriteExist
{
    use ClassBaseName;

    /**
     * Write the relationship exists field to the output.
     *
     * @param  array{name: string, type: string, related:string}  $relation
     * @return array{type: string, name: string}|string
     */
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $noExists = false, bool $optionalExists = false): array|string
    {
        $relationCase = Config::get('modeltyper.case.relations', 'snake');
        $columnsCase = Config::get('modeltyper.case.columns', 'snake');
        $name = app(MatchCase::class)($relationCase, $relation['name']);

        $relatedModel = $this->getClassName($relation['related']);
        $optional = $optionalExists ? '?' : '';

        $isExistable = in_array($relation['type'], [
            'BelongsToMany', 'HasMany', 'HasManyThrough',
            'MorphToMany', 'MorphMany', 'MorphedByMany',
        ]);

        $existsName = match ($columnsCase) {
            'camel' => Str::camel("{$name}Exists"),
            'pascal' => Str::studly("{$name}Exists"),
            'kebab' => Str::kebab("{$name}-exists"),
            default => "{$name}_exists",
        };

        $shouldAddExists = !$noExists && $isExistable;

        if ($jsonOutput) {
            if ($shouldAddExists) {
                return [
                    'name' => "{$existsName}{$optional}",
                    'type' => 'boolean',
                ];
            }
        }

        if ($shouldAddExists) {
            return "{$indent}  {$existsName}{$optional}: boolean" . PHP_EOL;
        }

        return "";
    }
}
