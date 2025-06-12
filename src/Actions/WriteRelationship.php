<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class WriteRelationship
{
    use ClassBaseName;

    /**
     * Write the relationship to the output.
     *
     * @param  array{name: string, type: string, related:string}  $relation
     * @return array{type: string, name: string}|string
     */
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $optionalRelation = false, bool $noCounts = false, bool $optionalCounts = false, bool $noExists = false, bool $optionalExists = false, bool $plurals = false): array|string
    {
        $relationCase = Config::get('modeltyper.case.relations', 'snake');
        $columnsCase = Config::get('modeltyper.case.columns', 'snake');
        $name = app(MatchCase::class)($relationCase, $relation['name']);

        $relatedModel = $this->getClassName($relation['related']);
        $optional = $optionalRelation ? '?' : '';

        // Use config if not explicitly provided
        $noCounts = $noCounts ?? Config::get('modeltyper.no-counts', false);
        $optionalCounts = $optionalCounts ?? Config::get('modeltyper.optional-counts', false);
        $noExists = $noExists ?? Config::get('modeltyper.no-exists', false);
        $optionalExists = $optionalExists ?? Config::get('modeltyper.optional-exists', false);

        // Determine if this is a countable relation
        $isCountable = in_array($relation['type'], [
            'BelongsToMany', 'HasMany', 'HasManyThrough',
            'MorphToMany', 'MorphMany', 'MorphedByMany',
        ]);

        // Handle no-counts option
        if ($noCounts && $isCountable) {
            $relationType = Str::singular($relatedModel);
            $shouldAddCount = false;
        } else {
            $relationType = match ($relation['type']) {
                'BelongsToMany', 'HasMany', 'HasManyThrough', 'MorphToMany', 'MorphMany', 'MorphedByMany' => $plurals === true ? Str::plural($relatedModel) : (Str::singular($relatedModel) . '[]'),
                'BelongsTo', 'HasOne', 'HasOneThrough', 'MorphOne', 'MorphTo' => Str::singular($relatedModel),
                default => $relatedModel,
            };
            $shouldAddCount = $isCountable;
        }

        // Handle optional-counts option
        if ($optionalCounts && $isCountable && ! $noCounts) {
            $optional = '?';
        }

        if (in_array($relation['type'], Config::get('modeltyper.custom_relationships.singular', []))) {
            $relationType = Str::singular($relation['type']);
            $shouldAddCount = false;
        }

        if (in_array($relation['type'], Config::get('modeltyper.custom_relationships.plural', []))) {
            $relationType = Str::singular($relation['type']);
            $shouldAddCount = false;
        }

        $countName = match ($columnsCase) {
            'camel' => Str::camel("{$name}Count"),
            'pascal' => Str::studly("{$name}Count"),
            'kebab' => Str::kebab("{$name}-count"),
            default => "{$name}_count",
        };

        $existsName = match ($columnsCase) {
            'camel' => Str::camel("{$name}Exists"),
            'pascal' => Str::studly("{$name}Exists"),
            'kebab' => Str::kebab("{$name}-exists"),
            default => "{$name}_exists",
        };

        // Determine if we should add exists field
        $shouldAddExists = ! $noExists && $isCountable;
        $optionalCounts = $optionalCounts ? '?' : '';
        $existsOptional = $optionalExists ? '?' : '';

        if ($jsonOutput) {
            $result = [
                'name' => "{$name}{$optional}",
                'type' => $relationType,
            ];

            // Add count field for countable relationships
            if ($shouldAddCount) {
                $result['count'] = [
                    'name' => "{$countName}{$optionalCounts}",
                    'type' => 'number',
                ];
            }

            // Add exists field for countable relationships
            if ($shouldAddExists) {
                $result['exists'] = [
                    'name' => "{$existsName}{$existsOptional}",
                    'type' => 'boolean',
                ];
            }

            return $result;
        }

        $output = "{$indent}  {$name}{$optional}: {$relationType}" . PHP_EOL;

        // Add count field for countable relationships
        if ($shouldAddCount) {
            $output .= "{$indent}  {$countName}{$optionalCounts}: number" . PHP_EOL;
        }

        // Add exists field for countable relationships
        if ($shouldAddExists) {
            $output .= "{$indent}  {$existsName}{$existsOptional}: boolean" . PHP_EOL;
        }

        return $output;
    }
}
