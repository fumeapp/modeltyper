<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;
use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;

class WriteColumnAttribute
{
    use ClassBaseName;

    /**
     * Get model columns and attributes to the output.
     *
     * @param  array  $attribute <{name: string, type: string, increments: bool, nullable: bool, default: mixed, unique: bool, fillable: bool, hidden: bool, appended: mixed, cast: string}>
     */
    public function __invoke(ReflectionClass $reflectionModel, array $attribute, string $indent = '', bool $jsonOutput = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false): array
    {
        $enumRef = null;
        $returnType = app(MapReturnType::class);

        $name = Str::snake($attribute['name']);
        $type = 'unknown';

        if ($noHidden && isset($attribute['hidden']) && $attribute['hidden']) {
            return [null, null];
        }

        if (isset($attribute['forceType'])) {
            $name = $attribute['name'];
            $type = $attribute['type'];
        } else {
            if (! is_null($attribute['cast']) && $attribute['cast'] !== $attribute['type']) {
                if (isset(TypescriptMappings::$mappings[$attribute['cast']])) {
                    $type = $returnType($attribute['cast'], $timestampsDate);
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
                                            $rt = $rf->getReturnType();
                                            $type = $returnType($rt->getName(), $timestampsDate);
                                            $enumRef = $this->resolveEnum($rt->getName());

                                            if ($enumRef) {
                                                $type = $this->getClassName($rt->getName());
                                            }

                                            if ($rt->allowsNull()) {
                                                $attribute['nullable'] = true;
                                            }
                                        }
                                    }
                                } else {
                                    $type = $this->getClassName($accessorMethod->getReturnType()->getName());
                                    $enumRef = $this->resolveEnum($accessorMethod->getReturnType()->getName());
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
                                    $type = $returnType($cleanStr, $timestampsDate);
                                } else {
                                    dump('Unknown cast type: ' . $attribute['cast']);
                                }
                            }
                        }
                    }
                }
            } else {
                $type = $returnType($attribute['type'], $timestampsDate);
            }
        }

        if ($attribute['nullable']) {
            $type .= '|null';
        }

        if ((isset($attribute['hidden']) && $attribute['hidden']) || ($optionalNullables && $attribute['nullable'])) {
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

    protected function resolveEnum(string $returnTypeName): ?ReflectionClass
    {
        try {
            $reflection = new ReflectionClass($returnTypeName);

            if ($reflection->isEnum()) {
                return $reflection;
            }
        } catch (ReflectionException $e) {
        }

        return null;
    }
}
