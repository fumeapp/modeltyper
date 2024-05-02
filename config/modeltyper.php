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
    | For example:
    | 'binary' => 'Blob',
    | 'point' => 'Point',
    | 'year' => 'string',
    */
    'custom_mappings' => [
        // 'binary' => 'Blob',
    ],

    /**
     * Custom relationships allows you to add support for relationships from
     * external packages that are not a part of the Laravel core. Note that
     * relationship method names are case sensitive.
     */
    'custom_relationships' => [
        'singular' => [
            // custom relationships that return a single model
            // 'belongsToThrough',
        ],
        'plural' => [
            // custom relationships that return multiple models
        ],
    ],
];
