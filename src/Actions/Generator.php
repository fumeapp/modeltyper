<?php

namespace FumeApp\ModelTyper\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Generator
{
    /**
     * Run the command to generate the output.
     *
     * @param  string|null  $specificModel
     * @param  bool  $global
     * @param  bool  $json
     * @return string
     */
    public function __invoke(?string $specificModel = null, bool $global = false, bool $json = false)
    {
        $models = $this->getModels($specificModel);

        return $this->display($models, $global, $json);
    }

    /**
     * Return collection of models.
     *
     * @param  string|null  $model
     * @return Collection<int, SplFileInfo>
     */
    protected function getModels(?string $model = null): Collection
    {
        return collect(File::allFiles(app_path('Models')))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
            ->when(
                $model,
                fn ($files, $model) => $files->filter(fn (SplFileInfo $file) => $file->getBasename('.php') === $model)
            )
            ->values();
    }

    /**
     * Display the command output.
     *
     * @param  Collection<int, SplFileInfo>  $models
     * @param  bool  $global
     * @param  bool  $json
     * @return string
     */
    protected function display(Collection $models, bool $global = false, bool $json = false): string
    {
        if ($models->isEmpty()) {
            return 'ERROR: No models found.';
        }

        if ($json) {
            return app(GenerateJsonOutput::class)($models);
        }

        return app(GenerateCliOutput::class)($models, $global);
    }
}
