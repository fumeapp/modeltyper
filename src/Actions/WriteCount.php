<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class WriteCount
{
    /**
     * Write the relationship counts to the output.
     *
     * @param  array{name: string, type: string, related:string}  $relation
     * @return array{type: string, name: string}|string
     */
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $optionalCounts = false): array|string
    {
        $relationCase = Config::get('modeltyper.case.relations', 'snake');
        $columnsCase = Config::get('modeltyper.case.columns', 'snake');
        $name = app(MatchCase::class)($relationCase, $relation['name']);

        $optional = $optionalCounts ? '?' : '';

        $isCountable = in_array($relation['type'], [
            'BelongsToMany', 'HasMany', 'HasManyThrough',
            'MorphToMany', 'MorphMany', 'MorphedByMany',
        ]);

        $countName = app(MatchCase::class)($columnsCase, "$name count");

        if ($jsonOutput) {
            if ($isCountable) {
                return [
                    'name' => "{$countName}{$optional}",
                    'type' => 'number',
                ];
            }
        }

        if ($isCountable) {
            return "{$indent}  {$countName}{$optional}: number" . PHP_EOL;
        }

        return '';
    }
}
