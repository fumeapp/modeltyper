<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class Generator
{
    /**
     * Run the command to generate the output.
     *
     * @return string
     */
    public function __invoke(?string $specificModel = null, bool $global = false, bool $json = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $resolveAbstract = false)
    {
        $models = app(GetModels::class)($specificModel);

        if ($models->isEmpty()) {
            $msg = 'No models found.';
            throw new ModelTyperException($msg);
        }

        return $this->display(
            $models,
            $global,
            $json,
            $plurals,
            $apiResources,
            $optionalRelations,
            $noRelations,
            $noHidden,
            $timestampsDate,
            $optionalNullables,
            $resolveAbstract
        );
    }

    /**
     * Display the command output.
     *
     * @param  Collection<int, SplFileInfo>  $models
     */
    protected function display(Collection $models, bool $global = false, bool $json = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $resolveAbstract = false): string
    {
        if ($json) {
            return app(GenerateJsonOutput::class)($models, $resolveAbstract);
        }

        return app(GenerateCliOutput::class)(
            $models,
            $global,
            $plurals,
            $apiResources,
            $optionalRelations,
            $noRelations,
            $noHidden,
            $timestampsDate,
            $optionalNullables,
            $resolveAbstract
        );
    }
}
