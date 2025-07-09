<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class GenerateCliOutput
{
    use ClassBaseName;
    use ModelRefClass;

    protected string $output = '';

    protected string $indent = '';

    /**
     * @var array<int, ReflectionClass>
     */
    protected array $enumReflectors = [];

    /**
     * @var array<int,  array<string, mixed>>
     */
    protected array $imports = [];

    /**
     * Output the command in the CLI.
     *
     * @param  Collection<int, SplFileInfo>  $models
     * @param  array<string, string>  $mappings
     *
     * @throws \ReflectionException
     */
    public function __invoke(
        Collection $models,
        array $mappings,
        bool $global = false,
        bool $useEnums = false,
        bool $plurals = false,
        bool $apiResources = false,
        bool $optionalRelations = false,
        bool $noRelations = false,
        bool $noHidden = false,
        bool $noCounts = false,
        bool $optionalCounts = false,
        bool $noExists = false,
        bool $optionalExists = false,
        bool $noSums = false,
        bool $optionalSums = false,
        bool $noAverages = false,
        bool $optionalAverages = false,
        bool $optionalNullables = false,
        bool $fillables = false,
        string $fillableSuffix = 'Fillable'
    ): string {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);
        $countWriter = app(WriteCount::class);
        $existWriter = app(WriteExist::class);
        $sumWriter = app(WriteSum::class);
        $avgWriter = app(WriteAvg::class);

        if ($global) {
            $namespace = Config::get('modeltyper.global-namespace', 'models');
            $this->output .= 'export {}' . PHP_EOL . 'declare global {' . PHP_EOL . "  export namespace {$namespace} {" . PHP_EOL . PHP_EOL;
            $this->indent = '    ';
        }

        $models->each(function (SplFileInfo $model) use ($mappings, $modelBuilder, $colAttrWriter, $relationWriter, $countWriter, $existWriter, $sumWriter, $avgWriter, $plurals, $apiResources, $optionalRelations, $noRelations, $noHidden, $noCounts, $optionalCounts, $noExists, $optionalExists, $noSums, $optionalSums, $noAverages, $optionalAverages, $optionalNullables, $fillables, $fillableSuffix, $useEnums) {
            $entry = '';
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
                'imports' => $imports,
                'sums' => $sums,
                'averages' => $averages,
            ] = $modelDetails;

            $this->imports = array_merge($this->imports, $imports->toArray());

            $entry .= "{$this->indent}export interface {$name} {" . PHP_EOL;

            if ($columns->isNotEmpty()) {
                $entry .= "{$this->indent}  // columns" . PHP_EOL;
                $columns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter, $noHidden, $optionalNullables, $mappings, $useEnums) {
                    [$line, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, mappings: $mappings, indent: $this->indent, noHidden: $noHidden, optionalNullables: $optionalNullables, useEnums: $useEnums);
                    if (! empty($line)) {
                        $entry .= $line;
                        if ($enum) {
                            $this->enumReflectors[] = $enum;
                        }
                    }
                });
            }

            if ($nonColumns->isNotEmpty()) {
                $entry .= "{$this->indent}  // mutators" . PHP_EOL;
                $nonColumns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter, $noHidden, $optionalNullables, $mappings, $useEnums) {
                    [$line, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, mappings: $mappings, indent: $this->indent, noHidden: $noHidden, optionalNullables: $optionalNullables, useEnums: $useEnums);
                    if (! empty($line)) {
                        $entry .= $line;
                        if ($enum) {
                            $this->enumReflectors[] = $enum;
                        }
                    }
                });
            }

            if ($interfaces->isNotEmpty()) {
                $entry .= "{$this->indent}  // overrides" . PHP_EOL;
                $interfaces->each(function ($interface) use (&$entry, $reflectionModel, $colAttrWriter, $mappings) {
                    [$line] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $interface, mappings: $mappings, indent: $this->indent);
                    $entry .= $line;
                });
            }

            if ($relations->isNotEmpty() && ! $noRelations) {
                $entry .= "{$this->indent}  // relations" . PHP_EOL;
                $relations->each(function ($rel) use (&$entry, $relationWriter, $optionalRelations, $plurals) {
                    $entry .= $relationWriter(relation: $rel, indent: $this->indent, optionalRelation: $optionalRelations, plurals: $plurals);
                });
            }

            if ($relations->isNotEmpty() && ! $noCounts) {
                $entry .= "{$this->indent}  // counts" . PHP_EOL;
                $relations->each(function ($rel) use (&$entry, $countWriter, $optionalCounts) {
                    $entry .= $countWriter(relation: $rel, indent: $this->indent, optionalCounts: $optionalCounts);
                });
            }

            if ($relations->isNotEmpty() && ! $noExists) {
                $entry .= "{$this->indent}  // exists" . PHP_EOL;
                $relations->each(function ($rel) use (&$entry, $existWriter, $optionalExists) {
                    $entry .= $existWriter(relation: $rel, indent: $this->indent, optionalExists: $optionalExists);
                });
            }

            if ($sums->isNotEmpty() && ! $noSums) {
                $entry .= "{$this->indent}  // sums" . PHP_EOL;
                $sums->each(function ($sum) use (&$entry, $sumWriter, $optionalSums) {
                    $entry .= $sumWriter(sum: $sum, indent: $this->indent, optionalSums: $optionalSums);
                });
            }

            if ($averages->isNotEmpty() && ! $noAverages) {
                $entry .= "{$this->indent}  // averages" . PHP_EOL;
                $averages->each(function ($avg) use (&$entry, $avgWriter, $optionalAverages) {
                    $entry .= $avgWriter(avg: $avg, indent: $this->indent, optionalAverages: $optionalAverages);
                });
            }

            $entry .= "{$this->indent}}" . PHP_EOL;

            if ($plurals) {
                $plural = Str::plural($name);
                $entry .= "{$this->indent}export type $plural = {$name}[]" . PHP_EOL;

                if ($apiResources) {
                    $entry .= "{$this->indent}export interface {$name}Results extends api.MetApiResults { data: $plural }" . PHP_EOL;
                }
            }

            if ($apiResources) {
                $entry .= "{$this->indent}export interface {$name}Result extends api.MetApiResults { data: $name }" . PHP_EOL;
                $entry .= "{$this->indent}export interface {$name}MetApiData extends api.MetApiData { data: $name }" . PHP_EOL;
                $entry .= "{$this->indent}export interface {$name}Response extends api.MetApiResponse { data: {$name}MetApiData }" . PHP_EOL;
            }

            if ($fillables) {
                $fillableAttributes = $reflectionModel->newInstanceWithoutConstructor()->getFillable();
                $fillablesUnion = implode(' | ', array_map(fn ($fillableAttribute) => "'$fillableAttribute'", $fillableAttributes));
                $entry .= "{$this->indent}export type {$name}{$fillableSuffix} = Pick<$name, $fillablesUnion>" . PHP_EOL;
            }

            $entry .= PHP_EOL;

            $this->output .= $entry;
        });

        collect($this->enumReflectors)
            ->unique(fn (ReflectionClass $reflector) => $reflector->getName())
            ->each(function (ReflectionClass $reflector) use ($useEnums) {
                $this->output .= app(WriteEnumConst::class)($reflector, $this->indent, false, $useEnums);
            });

        collect($this->imports)
            ->unique()
            ->each(function ($import) {
                $importTypeWithoutGeneric = Str::before($import['type'], '<');
                $entry = "import { {$importTypeWithoutGeneric} } from '{$import['import']}'" . PHP_EOL;
                $this->output = $entry . $this->output;
            });

        if ($global) {
            $this->output .= '  }' . PHP_EOL . '}' . PHP_EOL . PHP_EOL;
        }

        return substr($this->output, 0, strrpos($this->output, PHP_EOL));
    }
}
