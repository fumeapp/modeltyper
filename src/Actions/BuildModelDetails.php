<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Exceptions\AbstractModelException;
use FumeApp\ModelTyper\Exceptions\NestedCommandException;
use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @template TKey
 * @template TValue{reflectionModel: \ReflectionClass, name: string, columns: \Illuminate\Support\Collection, nonColumns: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, interfaces: \Illuminate\Support\Collection, imports: \Illuminate\Support\Collection}
 */
class BuildModelDetails
{
    use ClassBaseName;
    use ModelRefClass;

    /**
     * Build the model details.
     *
     * @return array<TKey, TValue>|null
     *
     * @throws ReflectionException
     */
    public function __invoke(SplFileInfo $modelFile, bool $resolveAbstract = false): ?array
    {
        $modelDetails = $this->getModelDetails($modelFile, $resolveAbstract);

        if ($modelDetails === null) {
            return null;
        }

        $reflectionModel = $this->getRefInterface($modelDetails);
        $laravelModel = $reflectionModel->newInstance();
        $databaseColumns = $laravelModel->getConnection()->getSchemaBuilder()->getColumnListing($laravelModel->getTable());

        $name = $this->getClassName($modelDetails['class']);
        $columns = collect($modelDetails['attributes'])->filter(fn ($att) => in_array($att['name'], $databaseColumns));
        $nonColumns = collect($modelDetails['attributes'])->filter(fn ($att) => ! in_array($att['name'], $databaseColumns));
        $relations = collect($modelDetails['relations']);
        $interfaces = collect($laravelModel->interfaces)->map(fn ($interface, $key) => [
            'name' => $key,
            'type' => $interface['type'] ?? 'unknown',
            'nullable' => $interface['nullable'] ?? false,
            'import' => $interface['import'] ?? null,
            'forceType' => true,
        ]);

        $imports = $interfaces->filter(function ($interface) {
            return isset($interface['import']);
        })
            ->map(function ($interface) {
                return [
                    'import' => $interface['import'],
                    'type' => $interface['type'],
                ];
            })
            ->unique()
            ->values();

        // Override all columns, mutators and relationships with custom interfaces
        $columns = $this->overrideCollectionWithInterfaces($columns, $interfaces);

        $nonColumns = $this->overrideCollectionWithInterfaces($nonColumns, $interfaces);

        $relations = $this->overrideCollectionWithInterfaces($relations, $interfaces);

        return [
            'reflectionModel' => $reflectionModel,
            'name' => $name,
            'columns' => $columns,
            'nonColumns' => $nonColumns,
            'relations' => $relations,
            'interfaces' => $interfaces->values(),
            'imports' => $imports,
        ];
    }

    /**
     * @return array<TKey, TValue>|null
     *
     * @throws NestedCommandException
     */
    private function getModelDetails(SplFileInfo $modelFile, bool $resolveAbstract): ?array
    {
        $modelFileArg = $modelFile->getRelativePathname();
        $modelFileArg = app()->getNamespace() . $modelFileArg;
        $modelFileArg = str_replace('.php', '', $modelFileArg);

        try {
            return app(RunModelShowCommand::class)($modelFileArg, $resolveAbstract);
        } catch (NestedCommandException $exception) {
            if ($exception->wasCausedBy(AbstractModelException::class) && ! $resolveAbstract) {
                return null;
            }
            throw $exception;
        }
    }

    private function overrideCollectionWithInterfaces(Collection $columns, Collection $interfaces): Collection
    {
        return $columns->map(function ($column) use ($interfaces) {
            $interfaces->each(function ($interface, $key) use (&$column, $interfaces) {
                if ($key === $column['name']) {
                    $column['type'] = $interface['type'];
                    $column['forceType'] = true;

                    $interfaces->forget($key);
                }
            });

            return $column;
        });
    }
}
