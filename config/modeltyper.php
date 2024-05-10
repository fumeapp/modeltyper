<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Override default mappings or add new ones
    |--------------------------------------------------------------------------
    |
    | Custom mappings allow you to add support for types that are considered
    | unknown or override existing mappings.
    |
    | You can also add mappings for your Custom Casts.
    |
    | For example:
    | 'App\Casts\YourCustomCast' => 'string|null',
    | 'binary' => 'Blob',
    | 'bool' => 'boolean',
    | 'point' => 'CustomPointInterface',
    | 'year' => 'string',
    */
    'custom_mappings' => [
        // 'binary' => 'Blob',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom relationships
    |--------------------------------------------------------------------------
    |
    | Custom relationships allows you to add support for relationships from
    | external packages that are not a part of the Laravel core.
    |
    | Note that relationship method names are case sensitive.
    |
    | singular: relationships that return a single model
    | plural:   relationships that return multiple models
    |
    | For example:
    |   'singular' => [
    |       'belongsToThrough',
    |   ],
    */
    'custom_relationships' => [
        'singular' => [
            // 'belongsToThrough',
        ],

        'plural' => [
            //
        ],
    ],
];
