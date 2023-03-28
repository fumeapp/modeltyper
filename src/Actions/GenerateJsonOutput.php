<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class GenerateJsonOutput
{
    protected array $output = [];

    protected array $enumReflectors = [];

    use ClassBaseName;
    use ModelRefClass;

    /**
     * Output the command in the CLI as JSON.
     *
     * @param  Collection<int, SplFileInfo>  $models
     * @return string
     */
    public function __invoke(Collection $models, bool $resolveAbstract = false): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);
        $enumWriter = app(WriteEnumConst::class);

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter, $resolveAbstract) {

            $modelDetails = $modelBuilder($model, $resolveAbstract);

            if($modelDetails === null) {
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
                ->map(function ($att) use ($reflectionModel, $colAttrWriter) {
                    [$property, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, jsonOutput: true);
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }

                    return $property;
                });

            $this->output['relations'] = $relations->map(function ($rel) use ($relationWriter, $name) {
                $relation = $relationWriter(relation: $rel, jsonOutput: true);

                return [
                    $relation['type'] => [
                        'name' => $relation['name'],
                        'type' => 'export type ' . $relation['type'] . ' = ' . 'Array<' . $name . '>',
                    ],
                ];
            });
        });

        $this->output['enums'] = collect($this->enumReflectors)->map(function ($enum) use ($enumWriter) {
            $enumConst = $enumWriter(reflection: $enum, jsonOutput: true);

            return [
                $enumConst['name'] => [
                    'name' => $enumConst['name'],
                    'type' => $enumConst['type'],
                ],
            ];
        });

        return json_encode($this->output) . PHP_EOL;
    }
}
