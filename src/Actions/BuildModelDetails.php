<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Traits\ClassBaseName;
use FumeApp\ModelTyper\Traits\ModelRefClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

class BuildModelDetails
{
    use ClassBaseName, ModelRefClass;

    /**
     * Build the model details.
     *
     * @return array{reflectionModel: \ReflectionClass, name: string, columns: \Illuminate\Support\Collection, nonColumns: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, interfaces: \Illuminate\Support\Collection, imports: \Illuminate\Support\Collection}|null
     *
     * @throws ReflectionException
     */
    public function __invoke(SplFileInfo $modelFile): ?array
    {
        $modelDetails = $this->getModelDetails($modelFile);

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

        $interfaces = collect($laravelModel->interfaces ?? [])->map(fn ($interface, $key) => [
            'name' => $key,
            'type' => $interface['type'] ?? 'unknown',
            'nullable' => $interface['nullable'] ?? false,
            'import' => $interface['import'] ?? null,
            'forceType' => true,
        ]);

        $imports = $interfaces
            ->filter(fn (array $interface): bool => isset($interface['import']))
            ->map(fn (array $interface): array => ['import' => $interface['import'], 'type' => $interface['type']])
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
            'interfaces' => $interfaces,
            'imports' => $imports,
        ];
    }

    /**
     * @return array{"class": class-string<\Illuminate\Database\Eloquent\Model>, database: string, table: string, policy: class-string|null, attributes: \Illuminate\Support\Collection, relations: \Illuminate\Support\Collection, events: \Illuminate\Support\Collection, observers: \Illuminate\Support\Collection, collection: class-string<\Illuminate\Database\Eloquent\Collection<\Illuminate\Database\Eloquent\Model>>, builder: class-string<\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>}|null
     */
    private function getModelDetails(SplFileInfo $modelFile): ?array
    {
        $modelFile = Str::of(app()->getNamespace())
            ->append($modelFile->getRelativePathname())
            ->replace('.php', '')
            ->toString();

        return app(RunModelInspector::class)($modelFile);
    }

    private function overrideCollectionWithInterfaces(Collection $columns, Collection $interfaces): Collection
    {
        return $columns->filter(function ($column) use ($interfaces) {
            $includeColumn = true;

            $interfaces->each(function ($interface, $key) use ($column, &$includeColumn) {
                if ($key === $column['name']) {
                    $includeColumn = false;
                }
            });

            return $includeColumn;
        });
    }
}
