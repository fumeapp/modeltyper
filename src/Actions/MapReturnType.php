<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;

class MapReturnType
{
    /**
     * Determine the type of accessor.
     *
     * @param  string  $returnType
     * @return string
     */
    public function __invoke(string $returnType): string
    {
        $mappings = TypescriptMappings::$mappings;

        $returnType = $returnType;
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
