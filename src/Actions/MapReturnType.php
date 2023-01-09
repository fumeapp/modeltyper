<?php

namespace FumeApp\ModelTyper\Actions;

use FumeApp\ModelTyper\Constants\TypescriptMappings;

class MapReturnType
{
    /**
     * Map the return type to a typescript type.
     *
     * @param  string  $returnType
     * @return string
     */
    public function __invoke(string $returnType): string
    {
        $mappings = TypescriptMappings::$mappings;

        $returnType = explode(' ', $returnType)[0];
        $returnType = explode('(', $returnType)[0];
        $returnType = strtolower($returnType);

        if ($returnType[0] === '?') {
            return $mappings[str_replace('?', '', $returnType)];
        }
        if (! isset($mappings[$returnType])) {
            return 'unknown';
        }

        return $mappings[$returnType];
    }
}
