<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
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
     */
    public function __invoke(Collection $models, bool $global = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $resolveAbstract = false, bool $fillables = false, string $fillableSuffix = 'Fillable'): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);

        if ($global) {
            $this->output .= "export {}\ndeclare global {\n  export namespace models {\n\n";
            $this->indent = '    ';
        }

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter, $plurals, $apiResources, $optionalRelations, $noRelations, $noHidden, $timestampsDate, $optionalNullables, $resolveAbstract, $fillables, $fillableSuffix) {
            $entry = '';
            $modelDetails = $modelBuilder($model, $resolveAbstract);

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
            ] = $modelDetails;

            $this->imports = array_merge($this->imports, $imports->toArray());

            $entry .= "{$this->indent}export interface {$name} {\n";

            if ($columns->isNotEmpty()) {
                $entry .= "{$this->indent}  // columns\n";
                $columns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter, $noHidden, $timestampsDate, $optionalNullables) {
                    [$line, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, indent: $this->indent, noHidden: $noHidden, timestampsDate: $timestampsDate, optionalNullables: $optionalNullables);
                    if (! empty($line)) {
                        $entry .= $line;
                        if ($enum) {
                            $this->enumReflectors[] = $enum;
                        }
                    }
                });
            }

            if ($nonColumns->isNotEmpty()) {
                $entry .= "{$this->indent}  // mutators\n";
                $nonColumns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter, $noHidden, $timestampsDate, $optionalNullables) {
                    [$line, $enum] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $att, indent: $this->indent, noHidden: $noHidden, timestampsDate: $timestampsDate, optionalNullables: $optionalNullables);
                    if (! empty($line)) {
                        $entry .= $line;
                        if ($enum) {
                            $this->enumReflectors[] = $enum;
                        }
                    }
                });
            }

            if ($interfaces->isNotEmpty()) {
                $entry .= "{$this->indent}  // overrides\n";
                $interfaces->each(function ($interface) use (&$entry, $reflectionModel, $colAttrWriter, $timestampsDate) {
                    [$line] = $colAttrWriter(reflectionModel: $reflectionModel, attribute: $interface, indent: $this->indent, timestampsDate: $timestampsDate);
                    $entry .= $line;
                });
            }

            if ($relations->isNotEmpty() && ! $noRelations) {
                $entry .= "{$this->indent}  // relations\n";
                $relations->each(function ($rel) use (&$entry, $relationWriter, $optionalRelations, $plurals) {
                    $entry .= $relationWriter(relation: $rel, indent: $this->indent, optionalRelation: $optionalRelations, plurals: $plurals);
                });
            }

            $entry .= "{$this->indent}}\n";

            if ($plurals) {
                $plural = Str::plural($name);
                $entry .= "{$this->indent}export type $plural = {$name}[]\n";

                if ($apiResources) {
                    $entry .= "{$this->indent}export interface {$name}Results extends api.MetApiResults { data: $plural }\n";
                }
            }

            if ($apiResources) {
                $entry .= "{$this->indent}export interface {$name}Result extends api.MetApiResults { data: $name }\n";
                $entry .= "{$this->indent}export interface {$name}MetApiData extends api.MetApiData { data: $name }\n";
                $entry .= "{$this->indent}export interface {$name}Response extends api.MetApiResponse { data: {$name}MetApiData }\n";
            }

            if ($fillables) {
                $fillableAttributes = $reflectionModel->newInstanceWithoutConstructor()->getFillable();
                $fillablesUnion = implode('|', array_map(fn ($fillableAttribute) => "'$fillableAttribute'", $fillableAttributes));
                $entry .= "{$this->indent}export type {$name}{$fillableSuffix} = Pick<$name, $fillablesUnion>\n";
            }

            $entry .= "\n";

            $this->output .= $entry;
        });

        collect($this->enumReflectors)
            ->unique(fn (ReflectionClass $reflector) => $reflector->getName())
            ->each(function (ReflectionClass $reflector) {
                $this->output .= app(WriteEnumConst::class)($reflector, $this->indent);
            });

        collect($this->imports)
            ->unique()
            ->each(function ($import) {
                $importTypeWithoutGeneric = Str::before($import['type'], '<');
                $entry = "import { {$importTypeWithoutGeneric} } from '{$import['import']}'\n";
                $this->output = $entry . $this->output;
            });

        if ($global) {
            $this->output .= "  }\n}\n\n";
        }

        return substr($this->output, 0, strrpos($this->output, "\n"));
    }
}
