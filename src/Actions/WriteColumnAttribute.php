<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;
use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionFunction;

class WriteColumnAttribute
{
    use ClassBaseName;

    /**
     * Get model columns and attributes to the output.
     *
     * @param  ReflectionClass  $reflectionModel
     * @param  array  $attribute <{name: string, type: string, increments: bool, nullable: bool, default: mixed, unique: bool, fillable: bool, hidden: bool, appended: mixed, cast: string}>
     * @param  string  $indent
     * @param  bool  $jsonOutput
     * @return array
     */
    public function __invoke(ReflectionClass $reflectionModel, array $attribute, string $indent = '', bool $jsonOutput = false): array
    {
        $enumRef = null;
        $returnType = app(MapReturnType::class);

        $name = Str::snake($attribute['name']);
        $type = 'unknown';

        if (isset($attribute['forceType'])) {
            $name = $attribute['name'];
            $type = $attribute['type'];
        } else {
            if (! is_null($attribute['cast']) && $attribute['cast'] !== $attribute['type']) {
                if (isset(TypescriptMappings::$mappings[$attribute['cast']])) {
                    $type = $returnType($attribute['cast']);
                } else {
                    if ($attribute['type'] === 'json' || $this->getClassName($attribute['cast']) === 'AsCollection' || $this->getClassName($attribute['cast']) === 'AsArrayObject') {
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
                                    $type = $this->getClassName($accessorMethod->getReturnType()->getName());
                                }
                            }
                        } else {
                            if (Str::contains($attribute['cast'], '\\')) {
                                $reflection = (new ReflectionClass($attribute['cast']));
                                if ($reflection->isEnum()) {
                                    $type = $this->getClassName($attribute['cast']);
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
        }

        if ($attribute['nullable']) {
            $type .= '|null';
            $name = "{$name}?";
        }

        if ($jsonOutput) {
            return [[
                'name' => $name,
                'type' => $type,
            ], $enumRef];
        }

        return ["{$indent}  {$name}: {$type}\n", $enumRef];
    }
}
