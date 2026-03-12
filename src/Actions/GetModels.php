<?php

namespace FumeApp\ModelTyper\Actions;

use Composer\ClassMapGenerator\ClassMapGenerator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class GetModels
{
    /**
     * Return collection of models.
     *
     * @param  string|class-string<Model>|null  $model
     * @param  list<string|class-string<Model>>|null  $includedModels
     * @param  list<string|class-string<Model>>|null  $excludedModels
     * @param  list<string>|null  $additionalPaths
     * @return Collection<int, SplFileInfo>
     */
    public function __invoke(?string $model = null, ?array $includedModels = null, ?array $excludedModels = null, ?array $additionalPaths = null): Collection
    {
        if (filled($model)) {
            $model = $this->resolveModelFilename($model);
        }

        if (filled($includedModels)) {
            $includedModels = array_map(fn (string $includedModel): string => $this->resolveModelFilename($includedModel), $includedModels);
        }

        if (filled($excludedModels)) {
            $excludedModels = array_map(fn (string $excludedModel): string => $this->resolveModelFilename($excludedModel), $excludedModels);
        }

        return collect([app_path()])
            ->when(
                $additionalPaths,
                fn (Collection $collection, array $paths): Collection => $collection->merge($paths)
            )
            ->map(fn (string $path): array => ClassMapGenerator::createMap($path))
            ->collapseWithKeys()
            ->flip()
            ->filter(fn (string $class): bool => class_exists($class) && (new ReflectionClass($class))->isSubclassOf(Model::class))
            ->map(fn (string $fqn): string => $this->resolveModelFilename($fqn))
            ->when(
                $includedModels,
                fn (Collection $files, array $includedModels): Collection => $files
                    ->filter(fn (string $class): bool => in_array($class, $includedModels))
            )
            ->when(
                $excludedModels,
                fn (Collection $files, array $excludedModels): Collection => $files
                    ->reject(fn (string $class): bool => in_array($class, $excludedModels))
            )
            ->when(
                $model,
                fn (Collection $files): Collection => $files
                    ->filter(fn (string $class): bool => $class === $model)
            )
            ->keys()
            ->map(fn (string $class): SplFileInfo => new SplFileInfo($class))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();
    }

    /**
     * @param  string|class-string<Model>  $model
     * @return non-empty-string
     *
     * @throws ModelTyperException if model param is empty
     */
    private function resolveModelFilename(string $model): string
    {
        throw_if(blank($model), ModelTyperException::class, 'Empty model name.');

        return Str::of($model)
            ->trim()
            ->classBasename()
            ->toString();
    }
}
