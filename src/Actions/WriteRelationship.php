<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Facades\App;
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
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $optionalRelation = false, ?bool $noCounts = null, ?bool $optionalCounts = null, bool $plurals = false): array|string
    {
        $case = Config::get('modeltyper.case.relations', 'snake');
        $name = App::make(MatchCase::class)($case, $relation['name']);

        $relatedModel = $this->getClassName($relation['related']);
        $optional = $optionalRelation ? '?' : '';

        // Use config if not explicitly provided
        $noCounts = $noCounts ?? Config::get('modeltyper.no-counts', false);
        $optionalCounts = $optionalCounts ?? Config::get('modeltyper.optional-counts', false);

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

        if ($jsonOutput) {
            $result = [
                'name' => "{$name}{$optional}",
                'type' => $relationType,
            ];

            // Add count field for countable relationships
            if ($shouldAddCount) {
                $countName = "{$name}_count";
                $result['count'] = [
                    'name' => "{$countName}",
                    'type' => 'number',
                ];
            }

            return $result;
        }

        $output = "{$indent}  {$name}{$optional}: {$relationType}" . PHP_EOL;

        // Add count field for countable relationships
        if ($shouldAddCount) {
            $countName = "{$name}_count";
            $output .= "{$indent}  {$countName}: number" . PHP_EOL;
        }

        return $output;
    }
}
