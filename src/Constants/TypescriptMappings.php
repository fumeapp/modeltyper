<?php

namespace FumeApp\ModelTyper\Constants;

class TypescriptMappings
{
    /**
     * @var array<string, string>
     */
    public static array $mappings = [
        'bigint' => 'number',
        'int' => 'number',
        'integer' => 'number',
        'text' => 'string',
        'float' => 'number',
        'string' => 'string',
        'decimal' => 'number',
        'datetime' => 'string',
        'date' => 'string',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'json' => 'Record<string, unknown>',
        'array' => 'string[]',
        'point' => 'Point',
        'guid' => 'string',
        'hashed' => 'string',
    ];
}
