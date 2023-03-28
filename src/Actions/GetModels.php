<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class GetModels
{
    /**
     * Return collection of models.
     *
     * @return Collection<int, SplFileInfo>
     */
    public function __invoke(?string $model = null, ?array $includedModels = null, ?array $excludedModels = null) : Collection
    {
        $modelShortName = $this->resolveModelFilename($model);

        if(! empty($includedModels)) {
            $includedModels = array_map(fn($includedModel) => $this->resolveModelFilename($includedModel), $includedModels);
        }

        if(! empty($excludedModels)) {
            $excludedModels = array_map(fn($excludedModel) => $this->resolveModelFilename($excludedModel), $excludedModels);
        }

        return collect(File::allFiles(app_path('Models')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
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

    private function resolveModelFilename(?string $model) : string|false
    {
        if($model === null) {
            return false;
        }

        return Str::contains($model, '\\') ? Str::afterLast($model, '\\') : $model;
    }
}
