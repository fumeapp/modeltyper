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
    public function __invoke(array $relation, string $indent = '', bool $jsonOutput = false): array|string
    {
        $name = Str::snake($relation['name']);
        $relatedModel = $this->getClassName($relation['related']);

        $relation = match ($relation['type']) {
            'BelongsToMany', 'HasMany', 'MorphToMany', 'MorphMany' => Str::plural($relatedModel),
            'BelongsTo', 'HasOne', 'MorphOne' => Str::singular($relatedModel),
            default => $relatedModel,
        };

        if ($jsonOutput) {
            return [
                'name' => $name,
                'type' => $relation,
            ];
        }

        return "{$indent}  {$name}: {$relation}\n";
    }
}
