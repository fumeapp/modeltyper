<?php

namespace FumeApp\ModelTyper\Actions;

use ErrorException;

class MapReturnType
{
    /**
     * Map the return type to a typescript type.
     *
     * @param  array<string, string>  $mappings
     *
     * @throws \ErrorException
     */
    public function __invoke(string $returnType, array $mappings): string
    {
        if (strlen($returnType) === 0) {
            throw new ErrorException('Empty string');
        }

        $returnType = explode(' ', $returnType)[0];
        $returnType = explode('(', $returnType)[0];
        $returnType = strtolower($returnType);

        if ($returnType[0] === '?') {
            return $mappings[str_replace('?', '', $returnType)] . ' | null';
        }

        if (! isset($mappings[$returnType])) {
            return 'unknown';
        }

        return $mappings[$returnType];
    }
}
