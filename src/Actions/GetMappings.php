<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;
use Illuminate\Support\Facades\Config;

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
            $mappings['immutable_datetime'] = 'Date';
            $mappings['immutable_custom_datetime'] = 'Date';
            $mappings['date'] = 'Date';
            $mappings['immutable_date'] = 'Date';
            $mappings['timestamp'] = 'Date';
        }

        return array_change_key_case(array_merge(
            $mappings,
            Config::get('modeltyper.custom_mappings', []),
        ), CASE_LOWER);
    }
}
