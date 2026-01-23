<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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

        $isNullable = $relation['nullable'] ?? false;
        $optional = $optionalRelation ? '?' : '';

        // For MorphTo relations with union types, use the related string directly
        if ($relation['type'] === 'MorphTo' && str_contains($relation['related'], '|')) {
            $relationType = $relation['related'];
        } else {
            $relatedModel = $this->getClassName($relation['related']);

            $relationType = match ($relation['type']) {
                'BelongsToMany', 'HasMany', 'HasManyThrough', 'MorphToMany', 'MorphMany', 'MorphedByMany' => $plurals === true ? Str::plural($relatedModel) : (Str::singular($relatedModel).'[]'),
                'BelongsTo', 'HasOne', 'HasOneThrough', 'MorphOne', 'MorphTo' => Str::singular($relatedModel),
                default => $relatedModel,
            };
        }

        if ($isNullable) {
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
