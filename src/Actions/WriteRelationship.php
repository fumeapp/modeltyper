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
     * @param  array{name: string, type: string, related:string, nullable?: bool}  $relation
     * @return array{type: string, name: string}|string
     */
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $optionalRelation = false, bool $plurals = false): array|string
    {
        $case = Config::get('modeltyper.case.relations', 'snake');
        $name = app(MatchCase::class)($case, $relation['name']);

        $relatedModel = $this->getClassName($relation['related']);

        // Check if the relation is nullable (either from the return type or from config)
        $isNullable = $relation['nullable'] ?? false;
        $optional = ($optionalRelation || $isNullable) ? '?' : '';

        $relationType = match ($relation['type']) {
            'BelongsToMany', 'HasMany', 'HasManyThrough', 'MorphToMany', 'MorphMany', 'MorphedByMany' => $plurals === true ? Str::plural($relatedModel) : (Str::singular($relatedModel).'[]'),
            'BelongsTo', 'HasOne', 'HasOneThrough', 'MorphOne', 'MorphTo' => Str::singular($relatedModel),
            default => $relatedModel,
        };

        // Add | null to the type if it's nullable and not already optional
        if ($isNullable && ! $optional) {
            $relationType .= ' | null';
        }

        if (in_array($relation['type'], Config::get('modeltyper.custom_relationships.singular', []))) {
            $relationType = Str::singular($relation['type']);
        }

        if (in_array($relation['type'], Config::get('modeltyper.custom_relationships.plural', []))) {
            $relationType = Str::singular($relation['type']);
        }

        if ($jsonOutput) {
            return [
                'name' => "{$name}{$optional}",
                'type' => $relationType,
            ];
        }

        return "{$indent}  {$name}{$optional}: {$relationType}".PHP_EOL;
    }
}
