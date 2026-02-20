<?php

namespace FumeApp\ModelTyper\Overrides;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelInspector as EloquentModelInspector;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use SplFileObject;

class ModelInspector extends EloquentModelInspector
{
    /**
     * Create a new model inspector instance.
     */
    public function __construct(?Application $app = null)
    {
        $this->relationMethods = collect(Arr::flatten(Config::get('modeltyper.custom_relationships', [])))
            ->map(fn (string $method): string => Str::trim($method))
            ->merge($this->relationMethods)
            ->toArray();

        parent::__construct($app ?? app());
    }

    /**
     * Get the relations from the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    protected function getRelations($model)
    {
        return (new \Illuminate\Support\Collection(get_class_methods($model)))
            ->map(fn ($method) => new ReflectionMethod($model, $method))
            ->reject(
                fn (ReflectionMethod $method) => $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() === \Illuminate\Database\Eloquent\Model::class
                    || $method->getNumberOfParameters() > 0
            )
            ->filter(function (ReflectionMethod $method) {
                if ($method->getReturnType() instanceof ReflectionNamedType
                    && is_subclass_of($method->getReturnType()->getName(), \Illuminate\Database\Eloquent\Relations\Relation::class)) {
                    return true;
                }

                $file = new SplFileObject($method->getFileName());
                $file->seek($method->getStartLine() - 1);
                $code = '';
                while ($file->key() < $method->getEndLine()) {
                    $code .= mb_trim($file->current());
                    $file->next();
                }

                return (new \Illuminate\Support\Collection($this->relationMethods))
                    ->contains(fn ($relationMethod) => str_contains($code, '$this->' . $relationMethod . '('));
            })
            ->map(function (ReflectionMethod $method) use ($model) {
                $relation = $method->invoke($model);

                if (! $relation instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                    return null;
                }

                // Check if the return type is nullable
                $nullable = $this->isReturnTypeNullable($method);

                $relationType = Str::afterLast(get_class($relation), '\\');
                $related = get_class($relation->getRelated());

                // For MorphTo relations, extract the union types from the return type
                if ($relation instanceof MorphTo) {
                    $related = $this->extractMorphToRelatedModels($method);
                }

                return [
                    'name' => $method->getName(),
                    'type' => $relationType,
                    'related' => $related,
                    'nullable' => $nullable,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Extract related models from a MorphTo relation's return type.
     */
    protected function extractMorphToRelatedModels(ReflectionMethod $method): string
    {
        $returnType = $method->getReturnType();

        if (! $returnType instanceof ReflectionUnionType) {
            return get_class($this->getRelatedModelFromMorphTo($method));
        }

        $types = [];
        foreach ($returnType->getTypes() as $type) {
            $typeName = $type->getName();
            // Skip the MorphTo type itself
            if ($typeName === 'Illuminate\Database\Eloquent\Relations\MorphTo') {
                continue;
            }
            $types[] = Str::afterLast($typeName, '\\');
        }

        return implode('|', $types);
    }

    /**
     * Get the related model from a MorphTo relation.
     */
    protected function getRelatedModelFromMorphTo(ReflectionMethod $method): ?\Illuminate\Database\Eloquent\Model
    {
        try {
            $model = $method->getDeclaringClass()->newInstance();
            $relation = $method->invoke($model);

            if ($relation instanceof MorphTo) {
                return $relation->getRelated();
            }
        } catch (\Exception $e) {
            // Return null if we can't instantiate the model
        }

        return null;
    }

    /**
     * Check if a method's return type is nullable.
     */
    protected function isReturnTypeNullable(ReflectionMethod $method): bool
    {
        $returnType = $method->getReturnType();

        if ($returnType === null) {
            return false;
        }

        // Check if it's a union type containing null
        if ($returnType instanceof ReflectionUnionType) {
            foreach ($returnType->getTypes() as $type) {
                if ($type->getName() === 'null') {
                    return true;
                }
            }
        }

        // Check if it's a single nullable type
        if ($returnType instanceof ReflectionNamedType) {
            return $returnType->allowsNull();
        }

        return false;
    }
}
