<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Support\Collection;

/**
 * @throws \FumeApp\ModelTyper\Exceptions\ModelTyperException
 */
class Generator
{
    /**
     * Run the command to generate the output.
     *
     * @return string
     */
    public function __invoke(?string $specificModel = null, bool $global = false, bool $json = false, bool $useEnums = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $fillables = false, string $fillableSuffix = 'Fillable')
    {
        $models = app(GetModels::class)($specificModel);

        if ($models->isEmpty()) {
            throw new ModelTyperException('No models found.');
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
            useEnums: $useEnums,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }

    /**
     * Return the command output.
     *
     * @param  Collection<int, \Symfony\Component\Finder\SplFileInfo>  $models
     */
    protected function display(Collection $models, bool $global = false, bool $json = false, bool $useEnums = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $fillables = false, string $fillableSuffix = 'Fillable'): string
    {
        $mappings = app(GetMappings::class)(setTimestampsToDate: $timestampsDate);

        if ($json) {
            return app(GenerateJsonOutput::class)(models: $models, mappings: $mappings, useEnums: $useEnums);
        }

        return app(GenerateCliOutput::class)(
            models: $models,
            mappings: $mappings,
            global: $global,
            useEnums: $useEnums,
            plurals: $plurals,
            apiResources: $apiResources,
            optionalRelations: $optionalRelations,
            noRelations: $noRelations,
            noHidden: $noHidden,
            optionalNullables: $optionalNullables,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }
}
