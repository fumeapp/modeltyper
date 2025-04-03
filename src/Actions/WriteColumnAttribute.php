<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;

class WriteColumnAttribute
{
    use ClassBaseName;

    /**
     * Get model columns and attributes to the output.
     *
     * @param  \ReflectionClass<\Illuminate\Database\Eloquent\Model>  $reflectionModel
     * @param  array{name: string, type: string, increments: bool, nullable: bool, default: mixed, unique: bool, fillable: bool, hidden?: bool, appended: mixed, cast?: string|null, forceType?: bool}  $attribute
     * @param  array<string, string>  $mappings
     * @return array{array{name: string, type: string}, ReflectionClass|null}|array{string, ReflectionClass|null}|array{null, null}
     */
    public function __invoke(ReflectionClass $reflectionModel, array $attribute, array $mappings, string $indent = '', bool $jsonOutput = false, bool $noHidden = false, bool $optionalNullables = false, bool $useEnums = false): array
    {
        $enumRef = null;
        $returnType = app(MapReturnType::class);

        $case = Config::get('modeltyper.case.columns', 'snake');
        $name = app(MatchCase::class)($case, $attribute['name']);

        $type = 'unknown';

        if ($noHidden && isset($attribute['hidden']) && $attribute['hidden']) {
            return [null, null];
        }

        if (isset($attribute['forceType'])) {
            $name = $attribute['name'];
            $type = $attribute['type'];
        } else {
            if (! is_null($attribute['cast']) && $attribute['cast'] !== $attribute['type']) {
                if (isset($mappings[strtolower($attribute['cast'])])) {
                    $type = $returnType($attribute['cast'], $mappings);
                } else {
                    if ($attribute['type'] === 'json' || $this->getClassName($attribute['cast']) === 'AsCollection' || $this->getClassName($attribute['cast']) === 'AsArrayObject') {
                        $type = $returnType('json', $mappings);
                    } else {
                        if ($attribute['cast'] === 'accessor' || $attribute['cast'] === 'attribute') {
                            /** @var \ReflectionMethod $accessorMethod */
                            $accessorMethod = app(DetermineAccessorType::class)($reflectionModel, $name);

                            $accessorMethodReturnType = $accessorMethod->getReturnType();

                            if (! is_null($accessorMethodReturnType) && $accessorMethodReturnType instanceof ReflectionNamedType) {
                                if ($accessorMethodReturnType->getName() === 'Illuminate\Database\Eloquent\Casts\Attribute') {
                                    $closure = call_user_func($accessorMethod->getClosure($reflectionModel->newInstance()), 1);

                                    if (! is_null($closure->get)) {
                                        $rt = (new ReflectionFunction($closure->get))->getReturnType();

                                        if (! is_null($rt) && $rt instanceof ReflectionNamedType) {
                                            $type = $returnType($rt->getName(), $mappings);
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
                                    $type = $this->getClassName($accessorMethodReturnType->getName());
                                    $enumRef = $this->resolveEnum($accessorMethodReturnType->getName());
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
                                $cleanStr = Str::of($attribute['cast'])->before(':')->lower()->toString();

                                if (isset($mappings[$cleanStr])) {
                                    $type = $returnType($cleanStr, $mappings);
                                } else {
                                    dump('Unknown cast type: ' . $attribute['cast']);
                                }
                            }
                        }
                    }
                }
            } else {
                $type = $returnType($attribute['type'], $mappings);
            }
        }

        if ($useEnums) {
            $type = $enumRef && $type ? ($type . 'Enum') : $type;
        }

        if ($attribute['nullable']) {
            $type .= ' | null';
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

        return ["{$indent}  {$this->ensurePropertyIsValid($name)}: {$type}" . PHP_EOL, $enumRef];
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

    /**
     * Transforms invalid javascript property to valid one by surrounding it with quotes.
     *
     * This function checks if the property starts with a number or symbols. If it does, it
     * surrounds the identifier with double quotes to make it a valid string key.
     */
    private function ensurePropertyIsValid(string $identifier): string
    {
        $firstCharacter = substr($identifier, 0, 1);

        if (! ctype_digit($identifier) && ctype_digit($firstCharacter)) {
            return "'$identifier'";
        }

        if (preg_match('/^[^a-zA-Z0-9_$]/', $firstCharacter)) {
            return "'$identifier'";
        }

        return $identifier;
    }
}
