<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;

class GetMappings
{
    /**
     * Merges mappings from Config with Constants from TypescriptMappings.
     *
     * @return array<string, string>
     */
    public function __invoke(bool $setTimestampsToDate = false): array
    {
        $mappings = TypescriptMappings::$mappings;

        if ($setTimestampsToDate) {
            $mappings['datetime'] = 'Date';
            $mappings['date'] = 'Date';
            $mappings['timestamp'] = 'Date';
        }

        return array_change_key_case(array_merge(
            $mappings,
            config('modeltyper.custom_mappings', []),
        ), CASE_LOWER);
    }
}
