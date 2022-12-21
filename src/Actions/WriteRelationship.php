<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ModelBaseName;
use Illuminate\Support\Str;

class WriteRelationship
{
    use ModelBaseName;

    /**
     * Create model relations.
     *
     * @param  string  $indent
     * @param  array  $relation <{name: string, type: string, related:string}>
     * @return string
     */
    public function __invoke(string $indent, array $relation): string
    {
        $name = Str::snake($relation['name']);
        $relatedModel = $this->getModelName($relation['related']);

        $relation = match ($relation['type']) {
            'BelongsToMany', 'HasMany', 'MorphToMany', 'MorphMany' => Str::plural($relatedModel),
            'BelongsTo', 'HasOne', 'MorphOne' => Str::singular($relatedModel),
            default => $relatedModel,
        };

        return "{$indent}  {$name}: {$relation}\n";
    }
}
