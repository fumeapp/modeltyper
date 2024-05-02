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
        'array' => 'string[]',
        'bigint' => 'number',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'collection' => 'Record<string, unknown>',
        'date' => 'string',
        'datetime' => 'string',
        'decimal' => 'number',
        'double' => 'number',
        'encrypted' => 'string',
        'float' => 'number',
        'guid' => 'string',
        'hashed' => 'string',
        'integer' => 'number',
        'json' => 'Record<string, unknown>',
        'numeric' => 'number',
        'object' => 'Record<string, unknown>',
        'string' => 'string',
        'text' => 'string',
        'timestamp' => 'string',

        // mappings for Laravel 11
        'char' => 'string',
        'enum' => 'string',
        'int' => 'number',
        'longtext' => 'string',
        'mediumint' => 'number',
        'mediumtext' => 'string',
        'smallint' => 'number',
        'tinyint' => 'boolean',
        'time' => 'string',
        'varchar' => 'string',
        'year' => 'number',
    ];

    /**
     * @return array<string, string>
     */
    public static function getMappings(): array
    {
        return array_change_key_case(array_merge(
            self::$mappings,
            config('modeltyper.custom_mappings', []),
        ), CASE_LOWER);
    }
}
