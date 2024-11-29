<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Generate typescript interfaces in a global namespace named models
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
    'global' => false,

    /*
    |--------------------------------------------------------------------------
    | Should echo the definitions into a file
    |--------------------------------------------------------------------------
    */
    'output-file' => false,

    /*
    |--------------------------------------------------------------------------
    | Output the result as json
    |--------------------------------------------------------------------------
    */
    'output-file-path' => './resources/js/types/models.d.ts',

    /*
    |--------------------------------------------------------------------------
    | Output the result as json
    |--------------------------------------------------------------------------
    */
    'json' => false,

    /*
    |--------------------------------------------------------------------------
    | Use typescript enums instead of object literals
    |--------------------------------------------------------------------------
    */
    'use-enums' => false,

    /*
    |--------------------------------------------------------------------------
    | Output model plurals
    |--------------------------------------------------------------------------
    */
    'plurals' => false,

    /*
    |--------------------------------------------------------------------------
    | Do not include relations
    |--------------------------------------------------------------------------
    */
    'no-relations' => false,

    /*
    |--------------------------------------------------------------------------
    | Make relations optional fields on the model type
    |--------------------------------------------------------------------------
    */
    'optional-relations' => false,

    /*
    |--------------------------------------------------------------------------
    | Do not include hidden model attributes
    |--------------------------------------------------------------------------
    */
    'no-hidden' => false,

    /*
    |--------------------------------------------------------------------------
    | Output timestamps as a Date object type
    |--------------------------------------------------------------------------
    */
    'timestamps-date' => false,

    /*
    |--------------------------------------------------------------------------
    | Output nullable attributes as optional fields
    |--------------------------------------------------------------------------
    */
    'optional-nullables' => false,

    /*
    |--------------------------------------------------------------------------
    | Output api.MetApi interfaces
    |--------------------------------------------------------------------------
    */
    'api-resources' => false,

    /*
    |--------------------------------------------------------------------------
    | Attempt to resolve abstract models
    |--------------------------------------------------------------------------
    */
    'resolve-abstract' => false,

    /*
    |--------------------------------------------------------------------------
    | Output model fillables
    |--------------------------------------------------------------------------
    */
    'fillables' => false,

    /*
    |--------------------------------------------------------------------------
    | fillable-suffix
    |--------------------------------------------------------------------------
    */
    'fillable-suffix' => '',

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
