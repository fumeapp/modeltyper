<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;

class MapReturnType
{
    /**
     * Map the return type to a typescript type.
     */
    public function __invoke(string $returnType, bool $timestampsDate = false): string
    {
        $mappings = TypescriptMappings::getMappings();

        if ($timestampsDate) {
            $mappings['datetime'] = 'Date';
            $mappings['date'] = 'Date';
        }

        $returnType = explode(' ', $returnType)[0];
        $returnType = explode('(', $returnType)[0];
        $returnType = strtolower($returnType);

        if ($returnType[0] === '?') {
            return $mappings[str_replace('?', '', $returnType)] . '|null';
        }

        if (! isset($mappings[$returnType])) {
            return 'unknown';
        }

        return $mappings[$returnType];
    }
}
