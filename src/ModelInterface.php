<?php


namespace FumeApp\ModelTyper;


use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Viny\PointType;

class ModelInterface
{
    public array $mappings = [
        'bigint' => 'number',
        'int' => 'number',
        'integer' => 'number',
        'text' => 'string',
        'string' => 'string',
        'decimal' => 'number',
        'datetime' => 'Date',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'json' => '[]',
        'array' => 'string[]',
        'point' =>  'Point',
    ];

    public array $imports = [];

    private string $space = '';


    public function __construct(
        private bool $global = false,
    )
    {
        Type::addType('point', PointType::class);
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'point');
    }

    /**
     * Combine all instances together
     * @return string
     * @throws ReflectionException
     */
    public function generate(): string
    {
        $models = $this->getModels();
        $allCode = $this->getImports($models);

        if ($this->global) {
            $allCode .= "export {}\ndeclare global {\n  export namespace models {\n\n";
            $this->space = '    ';
        }

        foreach ($models as $model) {
            $interface = $this->getInterface(new $model());
            $allCode .= $this->getCode($interface);
        }
        if ($this->global) {
            $allCode .= "  }\n}";
        }
        return substr($allCode, 0, strrpos($allCode, "\n"));
    }

    /**
     * Generate a list of imports from specified interfaces
     * @param Collection $models
     * @return string
     */
    private function getImports(Collection $models): string {
        $code = '';
        $imports = [];
        foreach ($models as $model) {
            if ($interfaces = (new $model())->interfaces) {
                foreach ($interfaces as $interface) {
                    if (isset($interface['import'])) {
                        $imports[ $interface[ 'import' ] ][] = $interface[ 'name' ];
                    }
                }
            }
        }
        foreach ($imports as $import=>$names) {
            $code .= "import { " . join(', ', array_unique($names)) . " } from '$import'\n";
        }
        return $code;
    }

    /**
     * Build an interface from a model
     * @param Model $model
     * @return TypescriptInterface
     * @throws ReflectionException
     * @throws Exception
     */
    private function getInterface(Model $model): TypescriptInterface
    {
        $columns = $this->getColumns($model);
        $mutators = $this->getMutators($model);
        $relations = $this->getRelations($model);
        $interfaces = $this->getInterfaces($model, $columns, $mutators, $relations);
        return new TypescriptInterface(
            name: (new ReflectionClass($model))->getShortName(),
            columns: $columns,
            mutators: $mutators,
            relations: $relations,
            interfaces: $interfaces,
        );
    }


    /**
     * Build TS code from an interface
     * @param TypescriptInterface $interface
     * @return string
     */
    private function getCode(TypescriptInterface $interface): string
    {
        $code = "{$this->space}export interface {$interface->name} {\n";
        if (count($interface->columns) > 0) {
            $code .= "{$this->space}  // columns\n";
            foreach ($interface->columns as $key => $value) {
                $code .= "{$this->space}  $key: $value\n";
            }
        }
        if (count($interface->mutators) > 0) {
            $code .= "{$this->space}  // mutators\n";
            foreach ($interface->mutators as $key => $value) {
                $code .= "{$this->space}  $key: $value\n";
            }
        }
        if (count($interface->relations) > 0) {
            $code .= "{$this->space}  // relations\n";
            foreach ($interface->relations as $key => $value) {
                $code .= "{$this->space}  $key: $value\n";
            }
        }
        if (count($interface->interfaces) > 0) {
            $code .= "{$this->space}  // interfaces\n";
            foreach ($interface->interfaces as $key => $value) {
                $code .= "{$this->space}  $key: $value\n";
            }
        }
        $code .= "{$this->space}}\n";
        $plural = Str::plural($interface->name);
        $code .= "{$this->space}export type $plural = Array<{$interface->name}>\n\n";
        return $code;
    }


    /**
     * Find and map relationships
     *
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    public function getRelations(Model $model): array
    {
        $relations = [];
        $methods = get_class_methods($model);
        foreach ($methods as $method) {
            $reflection = new ReflectionMethod($model, $method);
            if ($reflection->hasReturnType()) {
                if ($model->interfaces) {
                    foreach ($model->interfaces as $key => $value) {
                        if ($key === $method) {
                            if (isset($value['nullable']) && $value['nullable'] === true) {
                                $relations[ $key . '?' ] = $value[ 'name' ];
                            } else {
                                $relations[ $key ] = $value[ 'name' ];
                            }
                            continue 2;
                        }
                    }
                }
                $type = (string) $reflection->getReturnType();
                $code = file($reflection->getFileName())[$reflection->getEndLine()-2];
                preg_match('/\((.*?)::class/', $code, $matches);
                if ($matches && $matches[1]) {

                    if ($type === 'Illuminate\Database\Eloquent\Relations\BelongsTo' ||
                        $type === 'Illuminate\Database\Eloquent\Relations\HasOne' ||
                        $type === 'Illuminate\Database\Eloquent\Relations\MorphOne'
                    ) {
                        $relations[Str::snake($method)] = $matches[1];
                    }

                    if ($type === '?Illuminate\Database\Eloquent\Relations\BelongsTo' ||
                        $type === '?Illuminate\Database\Eloquent\Relations\HasOne' ||
                        $type === '?Illuminate\Database\Eloquent\Relations\MorphOne'
                    ) {
                        $relations[Str::snake($method) . '?'] = $matches[1];
                    }

                    if ($type === 'Illuminate\Database\Eloquent\Relations\BelongsToMany' ||
                        $type === 'Illuminate\Database\Eloquent\Relations\HasMany' ||
                        $type === 'Illuminate\Database\Eloquent\Relations\MorphToMany' ||
                        $type === 'Illuminate\Database\Eloquent\Relations\MorphMany'
                    ) {
                        if ($matches[1]) {
                            $relations[Str::snake($method)] = Str::plural($matches[1]);
                        }
                    }

                    if ($type === '?Illuminate\Database\Eloquent\Relations\BelongsToMany' ||
                        $type === '?Illuminate\Database\Eloquent\Relations\HasMany' ||
                        $type === '?Illuminate\Database\Eloquent\Relations\MorphToMany' ||
                        $type === '?Illuminate\Database\Eloquent\Relations\MorphMany'
                    ) {
                        if ($matches[1]) {
                            $relations[Str::snake($method) . '?'] = Str::plural($matches[1]);
                        }
                    }
                }
            }
        }
        return $relations;
    }

    /**
     * Return any other remaining interfaces
     *
     * @param Model $model
     * @param array $columns
     * @param array $mutators
     * @param array $relations
     * @return array
     */
    private function getInterfaces(Model $model, array $columns, array $mutators, array $relations): array
    {
        if (!isset($model->interfaces)) {
            return [];
        }
        $interfaces = [];
        foreach ($model->interfaces as $key=>$interface) {
            if (array_key_exists($key, $columns) || array_key_exists($key . '?', $columns)) {
                continue;
            }
            if (array_key_exists($key, $mutators) || array_key_exists($key . '?', $mutators)) {
                continue;
            }
                if (array_key_exists($key, $relations) || array_key_exists($key . '?', $relations)) {
                continue;
            }
            $interfaces[$key] = $interface['name'];
        }
        return $interfaces;
    }


    /**
     * Find and map our get mutators
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    public function getMutators(Model $model): array
    {
        $mutations = [];
        $mutators = $model->getMutatedAttributes();
        foreach ($mutators as $mutator) {

            if (isset($model->interfaces) && isset($model->interfaces[$mutator])) {
                $mutations[$mutator] = $model->interfaces[$mutator]['name'];
                continue;
            }

            $method = 'get' . $this->camelize($mutator) . 'Attribute';
            $reflection = new ReflectionMethod($model, $method);
            if (!$reflection->hasReturnType()) {
                throw new Exception(
                    "Model for table {$model->getTable()} has no return type for mutator: {$mutator}"
                );
            }
            $mutations[$mutator] = $this->mapReturnType((string) $reflection->getReturnType());
        }
        return $mutations;
    }

    /**
     * Properly map a return type
     * @param $returnType
     * @return string
     */
    private function mapReturnType($returnType): string
    {
        if ($returnType[0] === '?') {
            return $this->mappings[str_replace('?', '', $returnType)]  . '|null';
        }
        if (!isset($this->mappings[$returnType])) {
            return $returnType;
        }
        return $this->mappings[$returnType];
    }

    /**
     * Get columns with their mappings
     * @param Model $model
     * @return array
     * @throws Exception
     */
    private function getColumns(Model $model): array
    {
        $columns = [];
        foreach ($this->getColumnList($model) as $columnName) {

            try {
                $column = $this->getColumn($model, $columnName);
                if (isset($model->interfaces) && isset($model->interfaces[$columnName])) {
                    if ($column->getNotnull()) {
                        $columns [ $columnName ] = $model->interfaces[ $columnName ][ 'name' ];
                    } else {
                        $columns [ $columnName . '?' ] = $model->interfaces[ $columnName ][ 'name' ];
                    }
                    continue;
                }
                if (!isset($this->mappings[$column->getType()->getName()])) {
                  throw new Exception('Unknown type found: ' . $column->getType()->getName());
                } else {
                    if ($column->getNotnull()) {
                        $columns[ $columnName ] = $this->mappings[ $column->getType()->getName() ];
                    } else {
                        $columns[ $columnName . '?' ] = $this->mappings[ $column->getType()->getName() ];
                    }
                }
            } catch (Exception $exception) {
            }


        }
        return $columns;
    }

    /**
     * Get an array of columns
     * @param Model $model
     * @return array
     */
    private function getColumnList(Model $model): array
    {
        return $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
    }

    /**
     * Get column details
     * @param Model $model
     * @param string $column
     * @return Column
     */
    private function getColumn(Model $model, string $column): Column
    {
        return $model->getConnection()->getDoctrineColumn($model->getTable(), $column);
    }

    /**
     * Get a list of all models
     * @return Collection
     */
    private function getModels(): Collection
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                return sprintf(
                    '\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\')
                );
            })->filter(function ($class) {
                $valid = false;
                if (class_exists($class)) {
                    $reflection = new ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
                }
                return $valid;
            });
        return $models->values();
    }

    /**
     * under_scores to CamelCase
     * @param $input
     * @param string $separator
     * @return string
     */
    private function camelize($input, $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

}
