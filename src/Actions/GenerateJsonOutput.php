<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class GenerateJsonOutput
{
    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $output = [];

    /**
     * @var array<int, ReflectionClass>
     */
    protected array $enumReflectors = [];

    use ClassBaseName;
    use ModelRefClass;

    /**
     * Output the command in the CLI as JSON.
     *
     * @param  Collection<int, \Symfony\Component\Finder\SplFileInfo>  $models
     * @param  array<string, string>  $mappings
     */
    public function __invoke(Collection $models, array $mappings, bool $useEnums = false): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);
        $enumWriter = app(WriteEnumConst::class);

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter, $mappings, $useEnums) {
            $modelDetails = $modelBuilder($model);

            if ($modelDetails === null) {
                // skip iteration if model details could not be resolved
                return;
            }

            [
                'reflectionModel' => $reflectionModel,
                'name' => $name,
                'columns' => $columns,
                'nonColumns' => $nonColumns,
                'relations' => $relations,
                'interfaces' => $interfaces,
            ] = $modelDetails;

            $this->output['interfaces'][$name] = $columns
                ->merge($nonColumns)
                ->merge($interfaces)
                ->map(function ($att) use ($reflectionModel, $colAttrWriter, $mappings, $useEnums) {
                    [$property, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, mappings: $mappings, attribute: $att, jsonOutput: true, useEnums: $useEnums);
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }

                    return $property;
                })->toArray();

            $this->output['relations'] = $relations->map(function ($rel) use ($relationWriter, $name) {
                $relation = $relationWriter(relation: $rel, jsonOutput: true);

                return [
                    $relation['type'] => [
                        'name' => $relation['name'],
                        'type' => 'export type ' . $relation['type'] . ' = ' . 'Array<' . $name . '>',
                    ],
                ];
            })->toArray();
        });

        $this->output['enums'] = collect($this->enumReflectors)->map(function ($enum) use ($enumWriter, $useEnums) {
            $enumConst = $enumWriter(reflection: $enum, jsonOutput: true, useEnums: $useEnums);

            return [
                $enumConst['name'] => [
                    'name' => $enumConst['name'],
                    'type' => $enumConst['type'],
                ],
            ];
        })->toArray();

        return json_encode($this->output, \JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
