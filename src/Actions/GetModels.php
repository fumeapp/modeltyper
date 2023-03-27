<?php

namespace FumeApp\ModelTyper\Actions;

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
        return collect(File::allFiles(app_path('Models')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->when(
                $model,
                fn ($files, $model) => $files->filter(fn (SplFileInfo $file) => $file->getBasename('.php') === $model)
            )
            ->values();
    }
}
