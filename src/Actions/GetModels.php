<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class GetModels
{
    /**
     * Return collection of models.
     *
     * @param  list<string>|null  $includedModels
     * @param  list<string>|null  $excludedModels
     * @return Collection<int, SplFileInfo>
     */
    public function __invoke(?string $model = null, ?array $includedModels = null, ?array $excludedModels = null): Collection
    {
        $modelShortName = $this->resolveModelFilename($model);

        if (! empty($includedModels)) {
            $includedModels = array_map(fn ($includedModel) => $this->resolveModelFilename($includedModel), $includedModels);
        }

        if (! empty($excludedModels)) {
            $excludedModels = array_map(fn ($excludedModel) => $this->resolveModelFilename($excludedModel), $excludedModels);
        }

        return collect(File::allFiles(app_path()))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->filter(function (SplFileInfo $file) {
                $tokens = token_get_all(file_get_contents($file->getRealPath()));

                $isClassOrAbstract = false;
                foreach ($tokens as $token) {
                    if ($token[0] == T_CLASS) {
                        $isClassOrAbstract = true;
                        break;
                    }
                    if ($token[0] == T_ABSTRACT) {
                        $isClassOrAbstract = true;
                        break;
                    }
                }

                return $isClassOrAbstract;
            })
            ->filter(function (SplFileInfo $file) {
                $class = app()->getNamespace() . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($file->getPathname(), app_path() . DIRECTORY_SEPARATOR)
                );

                return class_exists($class) && (new ReflectionClass($class))->isSubclassOf(EloquentModel::class);
            })
            ->when($includedModels, function ($files, $includedModels) {
                return $files->filter(fn (SplFileInfo $file) => in_array($file->getBasename('.php'), $includedModels));
            })
            ->when($excludedModels, function ($files, $excludedModels) {
                return $files->filter(fn (SplFileInfo $file) => ! in_array($file->getBasename('.php'), $excludedModels));
            })
            ->when($modelShortName, function ($files, $modelShortName) {
                return $files->filter(fn (SplFileInfo $file) => $file->getBasename('.php') === $modelShortName);
            })
            ->values();
    }

    private function resolveModelFilename(?string $model): string|false
    {
        if ($model === null) {
            return false;
        }

        return Str::contains($model, '\\') ? Str::afterLast($model, '\\') : $model;
    }
}
