<?php

declare(strict_types=1);

namespace FumeApp\ModelTyper\Actions;

use const JSON_PRETTY_PRINT;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

final class GenerateJsonOutput
{
    use ClassBaseName;
    use ModelRefClass;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected array $output = [];

    /**
     * @var array<int, ReflectionClass>
     */
    protected array $enumReflectors = [];

    /**
     * Output the command in the CLI as JSON.
     *
     * @param  Collection<int, SplFileInfo>  $models
     * @param  array<string, string>  $mappings
     *
     * @throws ReflectionException
     */
    public function __invoke(Collection $models, array $mappings, bool $useEnums = false, bool $noCounts = false, bool $optionalCounts = false, bool $noExists = false, bool $optionalExists = false): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);
        $enumWriter = app(WriteEnumConst::class);

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter, $mappings, $useEnums, $noCounts, $optionalCounts, $noExists, $optionalExists) {
            $modelDetails = $modelBuilder(
                modelFile: $model,
                includedModels: Config::get('modeltyper.included_models', []),
                excludedModels: Config::get('modeltyper.excluded_models', []),
            );

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

            $this->output['relations'] = $relations->map(function ($rel) use ($relationWriter, $name, $noCounts, $optionalCounts, $noExists, $optionalExists) {
                $relation = $relationWriter(relation: $rel, jsonOutput: true, noCounts: $noCounts, optionalCounts: $optionalCounts, noExists: $noExists, optionalExists: $optionalExists);

                return [
                    $relation['type'] => [
                        'name' => $relation['name'],
                        'type' => 'export type '.$relation['type'].' = '.'Array<'.$name.'>',
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

        return json_encode($this->output, JSON_PRETTY_PRINT).PHP_EOL;
    }
}
