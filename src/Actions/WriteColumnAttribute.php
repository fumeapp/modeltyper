<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;
use FumeApp\ModelTyper\Traits\ModelBaseName;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionFunction;

class WriteColumnAttribute
{
    use ModelBaseName;

    /**
     * Get model columns and attributes.
     *
     * @param  ReflectionClass  $reflectionModel
     * @param  string  $indent
     * @param  array  $attribute <{name: string, type: string, increments: bool, nullable: bool, default: mixed, unique: bool, fillable: bool, hidden: bool, appended: mixed, cast: string}>
     * @return array
     */
    public function __invoke(ReflectionClass $reflectionModel, string $indent, array $attribute): array
    {
        $enumRef = null;
        $returnType = app(MapReturnType::class);

        $name = Str::snake($attribute['name']);
        $type = 'unknown';

        if (! is_null($attribute['cast']) && $attribute['cast'] !== $attribute['type']) {
            if (isset(TypescriptMappings::$mappings[$attribute['cast']])) {
                $type = $returnType($attribute['cast']);
            } else {
                if ($attribute['type'] === 'json' || $this->getModelName($attribute['cast']) === 'AsCollection' || $this->getModelName($attribute['cast']) === 'AsArrayObject') {
                    $type = $returnType('json');
                } else {
                    if ($attribute['cast'] === 'accessor' || $attribute['cast'] === 'attribute') {
                        $accessorMethod = app(DetermineAccessorType::class)($reflectionModel, $name);

                        if ($accessorMethod->getReturnType()) {
                            if ($accessorMethod->getReturnType()->getName() === 'Illuminate\Database\Eloquent\Casts\Attribute') {
                                $closure = call_user_func($accessorMethod->getClosure($reflectionModel->newInstance()), 1);

                                if (! is_null($closure->get)) {
                                    $rf = new ReflectionFunction($closure->get);
                                    if ($rf->hasReturnType()) {
                                        $type = $returnType($rf->getReturnType()->getName());
                                    }
                                }
                            } else {
                                $type = $this->getModelName($accessorMethod->getReturnType()->getName());
                            }
                        }
                    } else {
                        if (Str::contains($attribute['cast'], '\\')) {
                            $reflection = (new ReflectionClass($attribute['cast']));
                            if ($reflection->isEnum()) {
                                $type = $this->getModelName($attribute['cast']);
                                $enumRef = $reflection;
                            }
                        } else {
                            $cleanStr = Str::of($attribute['cast'])->before(':')->__toString();

                            if (isset(TypescriptMappings::$mappings[$cleanStr])) {
                                $type = $returnType($cleanStr);
                            } else {
                                dump('Unknown cast type: ' . $attribute['cast']);
                            }
                        }
                    }
                }
            }
        } else {
            $type = $returnType($attribute['type']);
        }

        if ($attribute['nullable']) {
            $type .= '|null';
            $name = "{$name}?";
        }

        return ["{$indent}  {$name}: {$type}\n", $enumRef];
    }
}
