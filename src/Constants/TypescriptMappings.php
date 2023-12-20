<?php

namespace FumeApp\ModelTyper\Constants;

/**
 * @see https://laravel.com/docs/10.x/eloquent-mutators#attribute-casting
 */
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
        'double' => 'number',
        'string' => 'string',
        'decimal' => 'number',
        'datetime' => 'string',
        'date' => 'string',
        'timestamp' => 'string',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'json' => 'Record<string, unknown>',
        'object' => 'Record<string, unknown>',
        'collection' => 'Record<string, unknown>',
        'array' => 'string[]',
        'point' => 'Point',
        'guid' => 'string',
        'hashed' => 'string',
        'encrypted' => 'string',
    ];
}
