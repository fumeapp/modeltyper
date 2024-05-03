<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Support\Collection;

class Generator
{
    /**
     * Run the command to generate the output.
     *
     * @return string
     */
    public function __invoke(?string $specificModel = null, bool $global = false, bool $json = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $resolveAbstract = false, bool $fillables = false, string $fillableSuffix = 'Fillable')
    {
        $models = app(GetModels::class)($specificModel);

        if ($models->isEmpty()) {
            $msg = 'No models found.';
            throw new ModelTyperException($msg);
        }

        return $this->display(
            models: $models,
            global: $global,
            json: $json,
            plurals: $plurals,
            apiResources: $apiResources,
            optionalRelations: $optionalRelations,
            noRelations: $noRelations,
            noHidden: $noHidden,
            timestampsDate: $timestampsDate,
            optionalNullables: $optionalNullables,
            resolveAbstract: $resolveAbstract,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }

    /**
     * Return the command output.
     *
     * @param  Collection<int, \Symfony\Component\Finder\SplFileInfo>  $models
     */
    protected function display(Collection $models, bool $global = false, bool $json = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $resolveAbstract = false, bool $fillables = false, string $fillableSuffix = 'Fillable'): string
    {
        $mappings = app(GetMappings::class)(setTimestampsToDate: $timestampsDate);

        if ($json) {
            return app(GenerateJsonOutput::class)(models: $models, mappings: $mappings, resolveAbstract: $resolveAbstract);
        }

        return app(GenerateCliOutput::class)(
            models: $models,
            mappings: $mappings,
            global: $global,
            plurals: $plurals,
            apiResources: $apiResources,
            optionalRelations: $optionalRelations,
            noRelations: $noRelations,
            noHidden: $noHidden,
            optionalNullables: $optionalNullables,
            resolveAbstract: $resolveAbstract,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }
}
