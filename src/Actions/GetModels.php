<?php

namespace FumeApp\ModelTyper\Actions;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class GetModels
{
    /**
     * Return collection of models.
     *
     * @param  list<string>|null  $includedModels
     * @param  list<string>|null  $excludedModels
     * @return Collection<int, SplFileInfo>
     */
    public function __invoke(?string $model = null, ?array $includedModels = null, ?array $excludedModels = null, ?array $additionalPaths = null): Collection
    {
        $modelShortName = $this->resolveModelFilename($model);

        if (! empty($includedModels)) {
            $includedModels = array_map(fn ($includedModel) => $this->resolveModelFilename($includedModel), $includedModels);
        }

        if (! empty($excludedModels)) {
            $excludedModels = array_map(fn ($excludedModel) => $this->resolveModelFilename($excludedModel), $excludedModels);
        }

        return collect($additionalPaths)->add(app_path())->map(
            fn ($file) => collect(ClassMapGenerator::createMap($file))
        )->collapseWithKeys()
            ->flip()
            ->filter(fn ($class) => class_exists($class) && (new ReflectionClass($class))->isSubclassOf(EloquentModel::class))
            ->map(fn ($fqn) => $this->resolveModelFilename($fqn))
            ->when($includedModels, fn ($files, $includedModels) => $files->filter(fn (string $class) => in_array($class, $includedModels)))
            ->when($excludedModels, fn ($files, $excludedModels) => $files->filter(fn (string $class) => ! in_array($class, $excludedModels)))
            ->when($modelShortName, fn ($files, $modelShortName) => $files->filter(fn (string $class) => $class === $modelShortName))
            ->keys()->map(
                fn ($file) => new SplFileInfo($file)
            )->values();
    }

    private function resolveModelFilename(?string $model): string|false
    {
        if ($model === null) {
            return false;
        }

        return Str::contains($model, '\\') ? Str::afterLast($model, '\\') : $model;
    }
}
