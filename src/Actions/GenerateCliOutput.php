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

    protected array $enumReflectors = [];

    /**
     * Output the command in the CLI.
     *
     * @param  Collection<int, SplFileInfo>  $models
     * @param  bool  $global
     * @return string
     */
    public function __invoke(Collection $models, bool $global = false): string
    {
        $modelBuilder = app(BuildModelDetails::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);

        if ($global) {
            $this->output .= "export {}\ndeclare global {\n  export namespace models {\n\n";
            $this->indent = '    ';
        }

        $models->each(function (SplFileInfo $model) use ($modelBuilder, $colAttrWriter, $relationWriter) {
            [
                'reflectionModel' => $reflectionModel,
                'name' => $name,
                'columns' => $columns,
                'nonColumns' => $nonColumns,
                'relations' => $relations,
            ] = $modelBuilder($model);

            $entry = "{$this->indent}export interface {$name} {\n";

            if ($columns->isNotEmpty()) {
                $entry .= "{$this->indent}  // columns\n";
                $columns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter) {
                    [$line, $enum] = $colAttrWriter($reflectionModel, $att, $this->indent);
                    $entry .= $line;
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }
                });
            }

            if ($nonColumns->isNotEmpty()) {
                $entry .= "{$this->indent}  // mutators\n";
                $nonColumns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter) {
                    [$line, $enum] = $colAttrWriter($reflectionModel, $att, $this->indent);
                    $entry .= $line;
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }
                });
            }

            if ($relations->isNotEmpty()) {
                $entry .= "{$this->indent}  // relations\n";
                $relations->each(function ($rel) use (&$entry, $relationWriter) {
                    $entry .= $relationWriter($rel, $this->indent);
                });
            }

            $entry .= "{$this->indent}}\n";

            $plural = Str::plural($name);
            $entry .= "{$this->indent}export type $plural = {$name}[]\n\n";

            $this->output .= $entry;
        });

        collect($this->enumReflectors)
            ->unique(fn (ReflectionClass $reflector) => $reflector->getName())
            ->each(function (ReflectionClass $reflector) {
                $this->output .= app(WriteEnumConst::class)($reflector, $this->indent);
            });

        if ($global) {
            $this->output .= "  }\n}\n\n";
        }

        return substr($this->output, 0, strrpos($this->output, "\n"));
    }
}
