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
    public function __invoke(?string $model = null) : Collection
    {
        $modelShortName = Str::contains($model, '\\') ? Str::afterLast($model, '\\') : $model;

        return collect(File::allFiles(app_path('Models')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->when($modelShortName, function ($files, $model) use ($modelShortName) {
                return $files->filter(fn (SplFileInfo $file) => $file->getBasename('.php') === $modelShortName);
            })
            ->values();
    }
}
