<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Str;

class WriteRelationship
{
    use ClassBaseName;

    /**
     * Write the relationship to the output.
     *
     * @param  array  $relation <{name: string, type: string, related:string}>
     * @param  string  $indent
     * @param  bool  $jsonOutput
     * @return array|string
     */
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false, bool $optionalRelation = false, bool $plurals = false): array|string
    {
        $name = Str::snake($relation['name']);
        $relatedModel = $this->getClassName($relation['related']);
        $optional = $optionalRelation ? '?' : '';

        $relationType = match ($relation['type']) {
            'BelongsToMany', 'HasMany', 'HasManyThrough', 'MorphToMany', 'MorphMany', 'MorphedByMany' => $plurals === true ? Str::plural($relatedModel) : (Str::singular($relatedModel) . '[]'),
            'BelongsTo', 'HasOne', 'HasOneThrough', 'MorphOne', 'MorphTo' => Str::singular($relatedModel),
            default => $relatedModel,
        };

        if(in_array($relation['type'], config('modeltyper.custom_relationships.singular', []))) {
            $relationType = Str::singular($relation['type']);
        }

        if(in_array($relation['type'], config('modeltyper.custom_relationships.plural', []))) {
            $relationType = Str::singular($relation['type']);
        }

        if ($jsonOutput) {
            return [
                'name' => $name,
                'type' => $relationType,
            ];
        }

        return "{$indent}  {$name}{$optional}: {$relationType}\n";
    }
}
