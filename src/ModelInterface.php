<?php


namespace FumeApp\ModelTyper;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
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
        'date' => 'Date',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'json' => '[]',
        'array' => 'string[]',
        'point' =>  'Point',
    ];

    public array $imports = [];
    public array $enums = [];

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
        $allCode .= $this->getCasts($models);

        if ($this->global) {
            $allCode .= "export {}\ndeclare global {\n  export namespace models {\n\n";
            $this->space = '    ';
        }

        foreach ($models as $model) {
            $interface = $this->getInterface(new $model());
            $allCode .= $this->getCode($interface);
        }
        if ($this->global) {
            $allCode .= "  }\n}\n\n";
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
        list(
            $interfaces,
            $columns,
            $mutators,
            $relations
            ) = $this->getInterfaces($model, $columns, $mutators, $relations);
        return new TypescriptInterface(
            name: (new ReflectionClass($model))->getShortName(),
            columns: $columns,
            mutators: $mutators,
            relations: $relations,
            interfaces: $interfaces,
        );
    }

    /**
     * Get all Casts for for models
     *
     * @param Collection $models - Collection of models
     * @return string
     */
    private function getCasts(Collection $models): string
    {
        $code = '';
        $casts = [];
        foreach ($models as $model) {
            $model = new $model();
            foreach ($model->getCasts() as $key => $value) {
                if (!class_exists($value)) {
                    continue;
                }
                $reflection = (new ReflectionClass($value));

                // if not an enum or already imported skip
                if (!$reflection->isEnum() || in_array($value, $casts)) {
                    continue;
                }

                $enum = (new ReflectionEnum($value));

                $docBlock = $this->getEnumDocBlock($enum);
                $casts[$enum->getShortName()]['comments'] = $docBlock;
                $enumValues = [];
                foreach ($enum->getConstants() as $case) {
                    $enumValues[] = [
                        'name' => $case->name,
                        'value' => $case->value,
                    ];
                }

                $casts[$enum->getShortName()]['values'] = $enumValues;
                $this->enums[] = $value;
            }
        }

        // Now Loop over casts and make them TS imports
        foreach ($casts as $key => $values) {
            $code .= "export enum $key {\n";
            foreach ($values['values'] as $key => $value) {
                $enumVal = $value['value'];
                if (is_string($value['value'])) {
                    $enumVal = "'$value[value]'";
                }

                // if comments exists and the key is the same as the value add it before the value
                if (! empty($values['comments'])) {
                    // loop over comments and find the comment for this value
                    foreach ($values['comments'] as $comment) {
                        if (strpos($comment, $value['name']) === 0) {
                            $comment = str_replace($value['name'], '', $comment);
                            $comment = preg_replace('/[^a-zA-Z0-9\s]/', '', $comment);
                            $comment = trim($comment);
                            $code .= "  /** $comment */\n";
                            break;
                        }
                    }
                }

                $code .= "  $value[name] = $enumVal,\n";
            }
            $code .= "}\n";
        }

        return $code;
    }

    /**
     * Extract Enum DocBlock comments
     * @param ReflectionEnum $enum
     * @return array
     */
    private function getEnumDocBlock(ReflectionEnum $enum): array
    {
        $comments = [];
        $docBlock = $enum->getDocComment();
        if ($docBlock) {
            $pattern = "#(@property+\s*[a-zA-Z0-9, ()_].*)#";
            preg_match_all($pattern, $docBlock, $matches, PREG_PATTERN_ORDER);
            $comments = array_map(fn ($match) => trim(str_replace('@property', '', $match)), $matches[0]);
        }

        return $comments;
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
        $code .= "{$this->space}export type $plural = {$interface->name}[]\n\n";
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
                $code = '';
                for ($i = $reflection->getStartLine(); $i <= $reflection->getEndLine(); $i++) {
                    $code .= file($reflection->getFileName())[$i];
                }
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
            return [ [], $columns, $mutators, $relations ];
        }
        $interfaces = [];
        foreach ($model->interfaces as $key=>$interface) {
            $interfaces[$key] = $interface['name'];

            if (array_key_exists($key, $columns)) { unset($columns[$key]); }
            if (array_key_exists($key . '?', $columns)) { unset($columns[$key . '?']); }

            if (array_key_exists($key, $mutators)) { unset($mutators[$key]); }
            if (array_key_exists($key . '?', $mutators)) { unset($mutators[$key . '?']); }

            if (array_key_exists($key, $relations)) { unset($relations[$key]); }
            if (array_key_exists($key . '?', $relations)) { unset($relations[$key . '?']); }

            $interfaces[$key] = $interface['name'];
        }
        return [ $interfaces, $columns, $mutators, $relations ];
    }


    /**
     * Find and map our get mutators
     * @param Model $model
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    public function getMutators(Model $model): array
    {
        $mutations = [];
        $mutators = $model->getMutatedAttributes();
        foreach ($mutators as $mutator) {
            $reflection = $this->determineAccessorType($model, $mutator);
            $returnType = (string) $reflection->getReturnType();

            // If Model is using v9 Attributes
            if ($returnType == 'Illuminate\Database\Eloquent\Casts\Attribute') {
                // Check to see if the Model has Custom interfaces & has the mutator set with its type
                if (isset($model->attrs) && isset($model->attrs[$mutator])) {
                    $mutations[$mutator] = $model->attrs[$mutator];
                    continue;
                }

                $closure = call_user_func($reflection->getClosure($model), 1);
                if (!is_null($closure->get)) {
                    $rf = new ReflectionFunction($closure->get);
                    if ($rf->hasReturnType()) {
                        $returnType = $rf->getReturnType()->getName();
                        $mutations[$mutator] = $this->mapReturnType($returnType);
                        continue;
                    }else {
                        // warn user to add return type to closure
                        throw new Exception('Unable to determine return type for ' . $mutator . ' Please add a return type to the get closure');
                    }
                }
                throw new Exception(
                    "Model for table {$model->getTable()} is using new mutator: {$mutator}. You must define them inside your models \$attrs array"
                );
            } else {
                if (isset($model->attrs) && isset($model->attrs[$mutator])) {
                    $mutations[$mutator] = $model->attrs[$mutator];
                    continue;
                }
                if (!$returnType) {
                    throw new Exception(
                        "Model for table {$model->getTable()} has no return type for mutator: {$mutator}"
                    );
                }
                $mutations[$mutator] = $this->mapReturnType((string) $returnType);
            }
        }
        return $mutations;
    }

    /**
     * Determine which Laravel Accessor type is used
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#defining-an-accessor
     * @param Model $model
     * @param string $mutator
     * @return ReflectionMethod
     * @throws Exception
     */
    private function determineAccessorType($model, $mutator): ReflectionMethod
    {
        // Try traditional
        try {
            $method = 'get' . $this->camelize($mutator) . 'Attribute';
            return new ReflectionMethod($model, $method);
        } catch (Exception $e) {}

        // Try new
        try {
            $method = $this->camelize($mutator);
            return new ReflectionMethod($model, $method);

        } catch (Exception $e) {}

        throw new Exception('Accessor method for ' . $mutator . ' on model '. get_class($model) . ' does not exist');
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
                        $columns[$columnName] = $model->interfaces[$columnName]['name'];
                    } else {
                        $columns[$columnName . '?'] = $model->interfaces[$columnName]['name'];
                    }
                    continue;
                }
                // If model has casts use them
                if ($model->hasCast($columnName) && in_array($model->getCasts()[$columnName], $this->enums)) {
                    $columns[$columnName] = Arr::last(explode('\\', $model->getCasts()[$columnName]));
                    continue;
                }
                if (!isset($this->mappings[$column->getType()->getName()])) {
                    throw new Exception('Unknown type found: ' . $column->getType()->getName());
                } else {
                    if ($column->getNotnull()) {
                        $columns[$columnName] = $this->mappings[$column->getType()->getName()];
                    } else {
                        $columns[$columnName . '?'] = $this->mappings[$column->getType()->getName()];
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
