<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;
use const JSON_PRETTY_PRINT;

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
     * @param Collection<int, SplFileInfo> $models
     * @param array<string, string> $mappings
     * @throws ReflectionException
     */
    public function __invoke(Collection $models, array $mappings, bool $useEnums = false, bool $noCounts = false, bool $optionalCounts = false, bool $noExists = false, bool $optionalExists = false, bool $noSums = false, bool $optionalSums = false): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);
        $enumWriter = app(WriteEnumConst::class);
        $countWriter = app(WriteCount::class);
        $existWriter = app(WriteExist::class);
        $sumWriter = app(WriteSum::class);

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter, $countWriter, $existWriter, $sumWriter, $mappings, $useEnums, $noCounts, $optionalCounts, $noExists, $optionalExists, $noSums, $optionalSums): void {
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
                'sums' => $sums,
            ] = $modelDetails;

            $this->output['interfaces'][$name] = $columns
                ->merge($nonColumns)
                ->merge($interfaces)
                ->map(function ($att) use ($reflectionModel, $colAttrWriter, $mappings, $useEnums) {
                    [$property, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, mappings: $mappings, jsonOutput: true, useEnums: $useEnums);
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }

                    return $property;
                })->toArray();

            if (! $noCounts) {
                $relations->each(function ($rel) use ($countWriter, $name, $optionalCounts) {
                    $countConst = $countWriter(relation: $rel, jsonOutput: true, optionalCounts: $optionalCounts);

                    if (! empty($countConst)) {
                        $this->output['interfaces'][$name][] = $countConst;
                    }
                });
            }

            if (! $noExists) {
                $relations->each(function ($rel) use ($existWriter, $name, $optionalExists) {
                    $existConst = $existWriter(relation: $rel, jsonOutput: true, optionalExists: $optionalExists);

                    if (! empty($existConst)) {
                        $this->output['interfaces'][$name][] = $existConst;
                    }
                });
            }

            if (! $noSums) {
                $sums->each(function ($sum) use ($sumWriter, $name, $optionalSums) {
                    $sumConst = $sumWriter(sum: $sum, jsonOutput: true, optionalSums: $optionalSums);

                    $this->output['interfaces'][$name][] = $sumConst;
                });
            }

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

        return json_encode($this->output, JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
