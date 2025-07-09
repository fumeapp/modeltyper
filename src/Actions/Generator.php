<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @throws ModelTyperException
 */
class Generator
{
    /**
     * Run the command to generate the output.
     *
     * @throws ModelTyperException
     * @throws ReflectionException
     */
    public function __invoke(?string $specificModel = null, bool $global = false, bool $json = false, bool $useEnums = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $noCounts = false, bool $optionalCounts = false, bool $noExists = false, bool $optionalExists = false, bool $noSums = false, bool $optionalSums = false, bool $noAverages = false, bool $optionalAverages = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $fillables = false, string $fillableSuffix = 'Fillable'): string
    {
        $models = app(GetModels::class)(
            model: $specificModel,
            includedModels: Config::get('modeltyper.included_models', []),
            excludedModels: Config::get('modeltyper.excluded_models', [])
        );

        if ($models->isEmpty()) {
            throw new ModelTyperException('No models found.');
        }

        return $this->display(
            models: $models,
            global: $global,
            json: $json,
            useEnums: $useEnums,
            plurals: $plurals,
            apiResources: $apiResources,
            optionalRelations: $optionalRelations,
            noRelations: $noRelations,
            noHidden: $noHidden,
            noCounts: $noCounts,
            optionalCounts: $optionalCounts,
            noExists: $noExists,
            optionalExists: $optionalExists,
            noSums: $noSums,
            optionalSums: $optionalSums,
            noAverages: $noAverages,
            optionalAverages: $optionalAverages,
            timestampsDate: $timestampsDate,
            optionalNullables: $optionalNullables,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }

    /**
     * Return the command output.
     *
     * @param  Collection<int, SplFileInfo>  $models
     *
     * @throws ReflectionException
     */
    protected function display(Collection $models, bool $global = false, bool $json = false, bool $useEnums = false, bool $plurals = false, bool $apiResources = false, bool $optionalRelations = false, bool $noRelations = false, bool $noHidden = false, bool $noCounts = false, bool $optionalCounts = false, bool $noExists = false, bool $optionalExists = false, bool $noSums = false, bool $optionalSums = false, bool $noAverages = false, bool $optionalAverages = false, bool $timestampsDate = false, bool $optionalNullables = false, bool $fillables = false, string $fillableSuffix = 'Fillable'): string
    {
        $mappings = app(GetMappings::class)(setTimestampsToDate: $timestampsDate);

        if ($json) {
            return app(GenerateJsonOutput::class)(models: $models, mappings: $mappings, useEnums: $useEnums, noCounts: $noCounts, optionalCounts: $optionalCounts, noExists: $noExists, optionalExists: $optionalExists, noSums: $noSums, optionalSums: $optionalSums);
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
            noCounts: $noCounts,
            optionalCounts: $optionalCounts,
            noExists: $noExists,
            optionalExists: $optionalExists,
            noSums: $noSums,
            optionalSums: $optionalSums,
            noAverages: $noAverages,
            optionalAverages: $optionalAverages,
            optionalNullables: $optionalNullables,
            fillables: $fillables,
            fillableSuffix: $fillableSuffix
        );
    }
}
