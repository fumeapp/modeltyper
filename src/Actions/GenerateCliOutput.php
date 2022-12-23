<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ModelBaseName;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class GenerateCliOutput
{
    use ModelBaseName;

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
        $modalShow = app(RunModelShowCommand::class);
        $colAttrWriter = app(WriteColumnAttribute::class);
        $relationWriter = app(WriteRelationship::class);

        if ($global) {
            $this->output .= "export {}\ndeclare global {\n  export namespace models {\n\n";
            $this->indent = '    ';
        }

        $models->each(function (SplFileInfo $model) use ($modalShow, $colAttrWriter, $relationWriter) {
            $modelDetails = $modalShow($model->getBasename('.php'));
            // dd($modelDetails);

            $entry = '';

            $reflectionModel = $this->getRefInterface($modelDetails);
            $laravelModel = $reflectionModel->newInstance();
            $databaseColumns = $laravelModel->getConnection()->getSchemaBuilder()->getColumnListing($laravelModel->getTable());

            $name = $this->getName($modelDetails);
            $columns = collect($modelDetails['attributes'])->filter(fn ($att) => in_array($att['name'], $databaseColumns));
            $nonColumns = collect($modelDetails['attributes'])->filter(fn ($att) => ! in_array($att['name'], $databaseColumns));
            $relations = collect($modelDetails['relations']);

            $entry = "{$this->indent}export interface {$name} {\n";

            if ($columns->isNotEmpty()) {
                $entry .= "{$this->indent}  // columns\n";
                $columns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter) {
                    [$line, $enum] = $colAttrWriter($reflectionModel, $this->indent, $att);
                    $entry .= $line;
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }
                });
            }

            if ($nonColumns->isNotEmpty()) {
                $entry .= "{$this->indent}  // mutators\n";
                $nonColumns->each(function ($att) use (&$entry, $reflectionModel, $colAttrWriter) {
                    [$line, $enum] = $colAttrWriter($reflectionModel, $this->indent, $att);
                    $entry .= $line;
                    if ($enum) {
                        $this->enumReflectors[] = $enum;
                    }
                });
            }

            if ($relations->isNotEmpty()) {
                $entry .= "{$this->indent}  // relations\n";
                $relations->each(function ($rel) use (&$entry, $relationWriter) {
                    $entry .= $relationWriter($this->indent, $rel);
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
                $this->output .= app(WriteEnumConst::class)($this->indent, $reflector);
            });

        if ($global) {
            $this->output .= "  }\n}\n\n";
        }

        return substr($this->output, 0, strrpos($this->output, "\n"));
    }

    /**
     * Get the reflection interface.
     *
     * @param  array  $info - The model details from the model:show command.
     * @return ReflectionClass
     */
    protected function getRefInterface(array $info): ReflectionClass
    {
        $class = $info['class'];

        return new ReflectionClass($class);
    }

    /**
     * Get the name of the model.
     *
     * @param  array  $info - The model details from the model:show command.
     * @return string
     */
    protected function getName(array $info): string
    {
        $class = $info['class'];

        return $this->getModelName($class);
    }
}
